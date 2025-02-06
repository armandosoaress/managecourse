<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use Paytabscom\Laravel_paytabs\Facades\paypage;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\UserCoupon;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\ProductCoupon;
use App\Models\PurchasedCourse;
use App\Models\Store;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Exception;

class PaytabController extends Controller
{
    public $paytab_profile_id, $paytab_server_key, $paytab_region, $is_enabled, $currency;

    public function paymentConfig()
    {
        if (Auth::check()) {
            $payment_setting = Utility::getAdminPaymentSetting();
            config([
                'paytabs.profile_id' => isset($payment_setting['paytab_profile_id']) ? $payment_setting['paytab_profile_id'] : '',
                'paytabs.server_key' => isset($payment_setting['paytab_server_key']) ? $payment_setting['paytab_server_key'] : '',
                'paytabs.region' => isset($payment_setting['paytab_region']) ? $payment_setting['paytab_region'] : '',
                'paytabs.currency' => !empty($payment_setting['currency']) ? $payment_setting['currency'] : 'USD',
            ]);
        }
    }
    public function planPayWithpaytab(Request $request)
    {
        try {
            $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
            $plan = Plan::find($planID);
            $this->paymentconfig();
            $user = Auth::user();
            if ($plan) {
                $get_amount = $plan->price;

                if (!empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if (!empty($coupons)) {
                        $usedCoupun = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $get_amount = $plan->price - $discount_value;

                        if ($coupons->limit == $usedCoupun) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                        if ($get_amount <= 0) {
                            $authuser = Auth::user();
                            $authuser->plan = $plan->id;
                            $authuser->save();
                            $assignPlan = $authuser->assignPlan($plan->id);
                            if ($assignPlan['is_success'] == true && !empty($plan)) {
                                if (!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '') {
                                    try {
                                        $authuser->cancel_subscription($authuser->id);
                                    } catch (\Exception $exception) {
                                        \Log::debug($exception->getMessage());
                                    }
                                }
                                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                                $userCoupon = new UserCoupon();
                                $userCoupon->user = $authuser->id;
                                $userCoupon->coupon = $coupons->id;
                                $userCoupon->order = $orderID;
                                $userCoupon->save();
                                PlanOrder::create(
                                    [
                                        'order_id' => $orderID,
                                        'name' => null,
                                        'email' => null,
                                        'card_number' => null,
                                        'card_exp_month' => null,
                                        'card_exp_year' => null,
                                        'plan_name' => $plan->name,
                                        'plan_id' => $plan->id,
                                        'price' => $get_amount == null ? 0 : $get_amount,
                                        'price_currency' => $this->currency,
                                        'txn_id' => '',
                                        'payment_type' => 'Paytab',
                                        'payment_status' => 'success',
                                        'receipt' => null,
                                        'user_id' => $authuser->id,
                                    ]
                                );
                                $assignPlan = $authuser->assignPlan($plan->id);
                                return redirect()->route('plans.index')->with('success', __('Plan Successfully Activated'));
                            }
                        }
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }
                try
                {
                    $coupon = (empty($request->coupon)) ? "0" : $request->coupon;
                        $pay = paypage::sendPaymentCode('all')
                            ->sendTransaction('sale')
                            ->sendCart(1, $get_amount, 'plan payment')
                            ->sendCustomerDetails(isset($user->name) ? $user->name : "", isset($user->email) ? $user->email : '', '', '', '', '', '', '', '')
                            ->sendURLs(
                                route('plan.paytab.success', ['success' => 1, 'data' => $request->all(), 'plan_id'=>$plan->id, 'amount'=> $get_amount, 'coupon'=> $coupon]),
                                route('plan.paytab.success', ['success' => 0, 'data' => $request->all(), 'plan_id'=>$plan->id, 'amount'=> $get_amount, 'coupon'=> $coupon])
                            )
                            ->sendLanguage('en')
                            ->sendFramed($on = false)
                            ->create_pay_page();
                            if(empty(trim($pay)))
                            {
                                return redirect()->back()->with("error", __('Apologies, but it seems that the payment credentials are incorrect, and the desired currency is currently unavailable.'));
                            }
                        return $pay;
                }
                catch(\Exception $e)
                {
                    return redirect()->route('plans.index')->with('error', $e);
                }
            } else {
                return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
            }
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }

    }
    public function PaytabGetPayment(Request $request)
    {
		$planId=$request->plan_id;
		$couponCode=$request->coupon;
		$getAmount=$request->amount;


        if ($couponCode != 0) {
            $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
            $request['coupon_id'] = $coupons->id;
        } else {
            $coupons = null;
        }

        $plan = Plan::find($planId);
        $user = auth()->user();
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        try {
            if ($request->respMessage == "Authorised") {
                $order = new PlanOrder();
                $order->order_id = $orderID;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $getAmount;
                $order->price_currency = $this->currency;
                $order->payment_type = __('Paytab');
                $order->payment_status = 'success';
                $order->txn_id = '';
                $order->receipt = '';
                $order->user_id = $user->id;
                $order->save();

                $assignPlan = $user->assignPlan($plan->id);

                $coupons = Coupon::find($request->coupon_id);
                if (!empty($request->coupon_id)) {
                    if (!empty($coupons)) {
                        $userCoupon = new UserCoupon();
                        $userCoupon->user = $user->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order = $orderID;
                        $userCoupon->save();
                        $usedCoupun = $coupons->used_coupon();
                        if ($coupons->limit <= $usedCoupun) {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }
                    }
                }

                if ($assignPlan['is_success']) {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                } else {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }

            } else {
                return redirect()->route('plans.index')->with('error', __('Your Transaction is fail please try again'));
            }
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function PayWithpaytab(Request $request, $slug)
    {
        try {
            $cart = session()->get($slug);

            if(!empty($cart) && isset($cart['products']))
            {
                $products = $cart['products'];
            }
            else
            {
                return redirect()->back()->with('error', __('Please add to product into cart'));
            }

            $store = Store::where('slug', $slug)->first();
            $companyPaymentSetting = Utility::getPaymentSetting($store->id);

            $get_amount    = 0;
            $sub_totalprice = 0;
            $product_name   = [];
            $product_id     = [];

            foreach ($products as $key => $product) {
                $product_name[] = $product['product_name'];
                $product_id[]   = $product['id'];
                $sub_totalprice += $product['price'];
                $get_amount    += $product['price'];
            }

            if ($products) {
                if (isset($cart['coupon']) && isset($cart['coupon'])) {
                    if ($cart['coupon']['coupon']['enable_flat'] == 'off') {
                        $discount_value = ($sub_totalprice / 100) * $cart['coupon']['coupon']['discount'];
                        $get_amount    = $sub_totalprice - $discount_value;
                    } else {
                        $discount_value = $cart['coupon']['coupon']['flat_discount'];
                        $get_amount    = $sub_totalprice - $discount_value;
                    }
                }
                $student = Auth::guard('students')->user();

                config([
                    'paytabs.profile_id' => isset($companyPaymentSetting['paytab_profile_id']) ? $companyPaymentSetting['paytab_profile_id'] : '',
                    'paytabs.server_key' => isset($companyPaymentSetting['paytab_server_key']) ? $companyPaymentSetting['paytab_server_key'] : '',
                    'paytabs.region' => isset($companyPaymentSetting['paytab_region']) ? $companyPaymentSetting['paytab_region'] : '',
                    'paytabs.currency' => $store->currency_code,
                ]);
                $pay = paypage::sendPaymentCode('all')
                    ->sendTransaction('sale')
                    ->sendCart(1, $get_amount, 'plan payment')
                    ->sendCustomerDetails(isset($student->name) ? $student->name : "", isset($student->email) ? $student->email : '', '', '', '', '', '', '', '')
                    ->sendURLs(
                        route('paytab.success', ['success' => 1, 'data' => $request->all(), 'slug'=>$slug, 'amount'=> $get_amount , 'product_id'=>$product_id]),
                        route('paytab.success', ['success' => 0, 'data' => $request->all(), 'slug'=>$slug, 'amount'=> $get_amount , 'product_id'=>$product_id])
                    )
                    ->sendLanguage('en')
                    ->sendFramed($on = false)
                    ->create_pay_page();

                return $pay;

            }
        }catch(Exception $e){
            return redirect()->back()->with('error', __($e));
        }
    }

    public function PaytabGetPaymentCallback(Request $request)
    {
        $slug=$request->slug;
		$getAmount=$request->amount;
        $product_id = $request->product_id;
        try{
            $store = Store::where('slug', $slug)->first();
            $cart = session()->get($slug);
            $products       = $cart['products'];
            if(isset($cart['coupon']['data_id']))
            {
                $coupon = ProductCoupon::where('id', $cart['coupon']['data_id'])->first();
            }
            else
            {
                $coupon = '';
            }

            if ($request->respMessage == "Authorised") {
                $student               = Auth::guard('students')->user();
                $order                  = new Order();
                $order->order_id        = time();
                $order->name            = isset($student->name) ? $student->name : '' ;
                $order->card_number     = '';
                $order->card_exp_month  = '';
                $order->card_exp_year   = '';
                $order->student_id      = isset($student->id) ? $student->id : '';
                $order->course          = json_encode($products);
                $order->price           = $getAmount;
                $order->coupon          = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
                $order->coupon_json     = json_encode($coupon);
                $order->discount_price  = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                $order->price_currency  = $store->currency_code;
                $order->txn_id          = isset($pay_id) ? $pay_id : '';
                $order->payment_type    = 'Paytab';
                $order->payment_status  = 'approved';
                $order->receipt         = '';
                $order->store_id         = $store['id'];
                $order->save();

                if ((!empty(Auth::guard('students')->user())) ){

                    foreach ($products as $course_id) {
                        $purchased_course = new PurchasedCourse();
                        $purchased_course->course_id  = $course_id['product_id'];
                        $purchased_course->student_id = $student->id;
                        $purchased_course->order_id   = $order->id;
                        $purchased_course->save();

                        $student = Student::where('id', $purchased_course->student_id)->first();
                        $student->courses_id = $purchased_course->course_id;
                        $student->save();
                    }
                }

                $uArr = [
                    'order_id' => $order->order_id,
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
                        $msg = redirect()->route(
                            'store-complete.complete', [
                                                        $store->slug,
                                                        Crypt::encrypt($order->id),
                                                    ]
                        )->with('success', __('Transaction has been success'));
                    } else {
                        $msg = redirect()->back()->with('error', __('Webhook call failed.'));
                    }
                }

                $msg = redirect()->route(
                    'store-complete.complete', [
                                                    $store->slug,
                                                    Crypt::encrypt($order->id),
                                                ]
                )->with('success', __('Transaction has been success'));

                session()->forget($slug);


                return $msg;
            } else {
                return redirect()->back()->with('error', __('Your Transaction is fail please try again'));
            }
        }catch(Exception $e){
            return redirect()->back()->with('error', __($e));
        }
    }

}
