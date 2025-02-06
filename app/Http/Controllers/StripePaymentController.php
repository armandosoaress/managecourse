<?php

namespace App\Http\Controllers;


use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\PurchasedCourse;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Session;
use Stripe;

class StripePaymentController extends Controller
{
    public $settings;


    public function index()
    {
        if(\Auth::user()->can('manage order'))
        {
            $objUser = \Auth::user();
            if($objUser->type == 'super admin')
            {
                $orders = Order::select(
                    [
                        'orders.*',
                        'users.name as user_name',
                    ]
                )->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->with('total_coupon_used','coupon_detail')->get();
            }
            else
            {
                $orders = Order::select(
                    [
                        'orders.*',
                        'users.name as user_name',
                    ]
                )->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->where('users.id', '=', $objUser->id)->with('total_coupon_used','coupon_detail')->get();
            }

            return view('order.index', compact('orders'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function stripe($code)
    {
        try {
            $plan_id = \Illuminate\Support\Facades\Crypt::decrypt($code);
        } catch(\RuntimeException $e) {
            return redirect()->back()->with('error',__('Permission denied.'));
        }
        $plan    = Plan::find($plan_id);
        if($plan)
        {
            $admin_payments_details = Utility::getAdminPaymentSetting();
            return view('plans/stripe', compact('plan','admin_payments_details'));
        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function stripePost(Request $request, $slug)
    {
        $cart     = session()->get($slug);
        $products = $cart['products'];
        if(isset($cart['coupon']))
        {
            $coupon = $cart['coupon']['coupon'];
        }
        else
        {
            $coupon = [];
        }
        $store        = Store::where('slug', $slug)->first();

        $student_id = Auth::guard('students')->user()->id;
        $course = [];
        $price = 0;
        foreach($products as $key => $value){
            $course[] = $value['id'];
            $price += $value['price'];
        }
        $store_payment_setting = Utility::getPaymentSetting($store->id);

        $coupon_id = null;
        if($products)
        {
            try
            {
                if(isset($cart['coupon']))
                {
                    if($cart['coupon']['coupon']['enable_flat'] == 'off')
                    {
                        $discount_value = ($price / 100) * $cart['coupon']['coupon']['discount'];
                        $price          = $price - $discount_value;
                    }
                    else
                    {
                        $discount_value = $cart['coupon']['coupon']['flat_discount'];
                        $price          = $price - $discount_value;
                    }
                }
                $price = number_format($price, 2);
                $price = str_replace(',','',$price);
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                if($price > 0.00)
                {
                    Stripe\Stripe::setApiKey($store_payment_setting['stripe_secret']);
                    try
                    {
                        $data = Stripe\Charge::create(
                            [
                                "amount" => 100 * $price,
                                "currency" => 'INR',
                                "source" => $request->stripeToken,
                                "description" => " Stripe payment of order - " . $orderID,
                                "metadata" => ["order_id" => $orderID],
                            ]
                        );
                    }catch(\Exception $exception){

                    }
                }
                else
                {
                    $data['amount_refunded'] = 0;
                    $data['failure_code']    = '';
                    $data['paid']            = 1;
                    $data['captured']        = 1;
                    $data['status']          = 'succeeded';
                }
                if($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1)
                {
                    $order = Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => $request->name,
                            'card_number' => isset($data['payment_method_details']['card']['last4']) ? $data['payment_method_details']['card']['last4'] : '',
                            'card_exp_month' => isset($data['payment_method_details']['card']['exp_month']) ? $data['payment_method_details']['card']['exp_month'] : '',
                            'card_exp_year' => isset($data['payment_method_details']['card']['exp_year']) ? $data['payment_method_details']['card']['exp_year'] : '',
                            'student_id' => $student_id,
                            'course' => json_encode($products),
                            'price' => $price,
                            'coupon' => !empty($cart['coupon']['coupon']['id']) ? $cart['coupon']['coupon']['id'] : '',
                            'coupon_json' => json_encode($coupon),
                            'discount_price' => !empty($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '',
                            'price_currency' => $store->currency,
                            'txn_id' => isset($data['balance_transaction']) ? $data['balance_transaction'] : '',
                            'payment_type' => __('STRIPE'),
                            'payment_status' => isset($data['status']) ? $data['status'] : 'succeeded',
                            'receipt' => isset($data['receipt_url']) ? $data['receipt_url'] : 'free coupon',
                            'store_id' => $store['id'],
                        ]
                    );
                    foreach($course as $course_id){
                        $purchased_course = new PurchasedCourse();
                        $purchased_course->course_id = $course_id;
                        $purchased_course->student_id = $student_id;
                        $purchased_course->order_id = $order->id;
                        $purchased_course->save();

                        $student=Student::where('id',$purchased_course->student_id)->first();
                        $student->courses_id=$purchased_course->course_id;
                        $student->save();
                    }
                    session()->forget($slug);

                    $uArr = [
                        'order_id' => $orderID,
                        'store_name'  => $store['name'],
                    ];

                    // slack //
                    $settings  = Utility::notifications($store->id);
                    if(isset($settings['order_notification']) && $settings['order_notification'] ==1){
                        Utility::send_slack_msg('new_order',$uArr,$store->created_by);
                    }

                    // telegram //
                    $settings  = Utility::notifications($store->id);
                    if(isset($settings['telegram_order_notification']) && $settings['telegram_order_notification'] ==1){
                        Utility::send_telegram_msg('new_order',$uArr,$store->created_by);
                    }

                    //webhook
                    $module = 'New Order';
                    $webhook =  Utility::webhookSetting($module,$store->created_by);
                    if ($webhook) {
                        $parameter = json_encode($order);
                        // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                        $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                        if ($status == true) {
                            return redirect()->route(
                                'store-complete.complete', [
                                                             $store->slug,
                                                             Crypt::encrypt($order->id),
                                                         ]
                            )->with('success', __('Transaction has been success'));
                        } else {
                            return redirect()->back()->with('error', __('Webhook call failed.'));
                        }
                    }


                    return redirect()->route(
                        'store-complete.complete', [
                                                     $store->slug,
                                                     Crypt::encrypt($order->id),
                                                 ]
                    )->with('success', __('Transaction has been success'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Transaction has been failed.'));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->back()->with('error', __($e->getMessage()));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function addPayment(Request $request)
    {
        $objUser = \Auth::user();
        $planID  = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan    = Plan::find($planID);
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $setting = Utility::settings();
        if($plan)
        {
            try
            {
                $price = $plan->price;
                if(!empty($request->coupon))
                {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if(!empty($coupons))
                    {
                        $usedCoupun     = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $price          = $plan->price - $discount_value;

                        if($coupons->limit == $usedCoupun)
                        {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));




                if($price > 0.0)
                {
                    Stripe\Stripe::setApiKey($admin_payment_setting['stripe_secret']);

                    $data = Stripe\Charge::create(
                        [
                            "amount" => 100 * $price,
                            "source" => $request->stripeToken,
                            "description" => " Plan - " . $plan->name,
                            "metadata" => ["order_id" => $orderID],
                            "currency" => $admin_payment_setting['currency'],
                        ]
                    );

                }
                else
                {
                    $data['amount_refunded'] = 0;
                    $data['failure_code']    = '';
                    $data['paid']            = 1;
                    $data['captured']        = 1;
                    $data['status']          = 'succeeded';
                }

                if($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1)
                {

                    PlanOrder::create(
                        [
                            'order_id' => $orderID,
                            'name' => $request->name,
                            'card_number' => isset($data['payment_method_details']['card']['last4']) ? $data['payment_method_details']['card']['last4'] : '',
                            'card_exp_month' => isset($data['payment_method_details']['card']['exp_month']) ? $data['payment_method_details']['card']['exp_month'] : '',
                            'card_exp_year' => isset($data['payment_method_details']['card']['exp_year']) ? $data['payment_method_details']['card']['exp_year'] : '',
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $price,
                            'price_currency' => $admin_payment_setting['currency'],
                            'txn_id' => isset($data['balance_transaction']) ? $data['balance_transaction'] : '',
                            'payment_type' => __('STRIPE'),
                            'payment_status' => isset($data['status']) ? $data['status'] : 'succeeded',
                            'receipt' => isset($data['receipt_url']) ? $data['receipt_url'] : 'free coupon',
                            'user_id' => $objUser->id,
                        ]
                    );

                    if(!empty($request->coupon))
                    {
                        $userCoupon         = new UserCoupon();
                        $userCoupon->user   = $objUser->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order  = $orderID;
                        $userCoupon->save();

                        $usedCoupun = $coupons->used_coupon();
                        if($coupons->limit <= $usedCoupun)
                        {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }

                    }
                    if($data['status'] == 'succeeded')
                    {
                        $assignPlan = $objUser->assignPlan($plan->id);
                        if($assignPlan['is_success'])
                        {
                            return redirect()->route('plans.index')->with('success', __('Plan successfully activated.'));
                        }
                        else
                        {
                            return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                        }
                    }
                    else
                    {
                        return redirect()->route('plans.index')->with('error', __('Your payment has failed.'));
                    }
                }
                else
                {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }
}
