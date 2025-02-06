<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\PurchasedCourse;
use App\Models\Store;
use App\Models\Student;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Xendit\Xendit;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class XenditController extends Controller
{
    public function planPayWithXendit(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $xendit_api = $payment_setting['xendit_api'];
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';

        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $user = Auth::user();
        if ($plan) {
            $get_amount = $plan->price;

            if (!empty($request->coupon)) {
                $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun = $coupons->used_coupon();
                    $discount_value = ($plan->price / 100) * $coupons->discount;
                    $get_amount = $plan->price - $discount_value;
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $userCoupon = new UserCoupon();
                    $userCoupon->user = Auth::user()->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->order = $orderID;
                    $userCoupon->save();
                    if ($coupons->limit == $usedCoupun) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }
            $response = ['orderId' => $orderID, 'user' => $user, 'get_amount' => $get_amount, 'plan' => $plan, 'currency' => $currency];
            Xendit::setApiKey($xendit_api);
            $params = [
                'external_id' => $orderID,
                'payer_email' => Auth::user()->email,
                'description' => 'Payment for order ' . $orderID,
                'amount' => $get_amount,
                'callback_url' =>  route('plan.xendit.status'),
                'success_redirect_url' => route('plan.xendit.status', $response),
                'failure_redirect_url' => route('plans.index'),
            ];

            $invoice = \Xendit\Invoice::create($params);
            Session::put('invoice',$invoice);

            return redirect($invoice['invoice_url']);
        }
    }

    public function planGetXenditStatus(Request $request)
    {
        $data = request()->all();

        $fixedData = [];
        foreach ($data as $key => $value) {
            $fixedKey = str_replace('amp;', '', $key);
            $fixedData[$fixedKey] = $value;
        }
        $payment_setting = Utility::getAdminPaymentSetting();
        $xendit_api = $payment_setting['xendit_api'];
        Xendit::setApiKey($xendit_api);

        $session = Session::get('invoice');
        $getInvoice = \Xendit\Invoice::retrieve($session['id']);

        $authuser = User::find($fixedData['user']);
        $plan = Plan::find($fixedData['plan']);

        if($getInvoice['status'] == 'PAID'){

            PlanOrder::create(
                [
                    'order_id' => $fixedData['orderId'],
                    'name' => null,
                    'email' => null,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->id,
                    'price' => $fixedData['get_amount'] == null ? 0 : $fixedData['get_amount'],
                    'price_currency' => $fixedData['currency'],
                    'txn_id' => '',
                    'payment_type' => __('Xendit'),
                    'payment_status' => 'succeeded',
                    'receipt' => null,
                    'user_id' => $fixedData['user'],
                ]
            );

            $assignPlan = $authuser->assignPlan($plan->id);

            if($assignPlan['is_success'])
            {
                return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
            }
            else
            {
                return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
            }
        }
    }
    public function PayWithXendit(Request $request,$slug)
    {
        $cart     = session()->get($slug);
        $products = $cart['products'];

        $store = Store::where('slug', $slug)->first();
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $total_price    = 0;
        $sub_totalprice = 0;
        $product_name   = [];
        $product_id     = [];

        foreach ($products as $key => $product) {
            $product_name[] = $product['product_name'];
            $product_id[]   = $product['id'];
            $sub_totalprice += $product['price'];
            $total_price    += $product['price'];
        }

        try {
            if ($products) {
                $payment_setting = Utility::getPaymentSetting($store->id);
                $xendit_token = $payment_setting['xendit_token'];
                $xendit_api = $payment_setting['xendit_api'];
                $response = ['orderId' => $orderID, 'slug' => $slug, 'get_amount' => $total_price, 'currency' => $store->currency_code];
                $student = Auth::guard('students')->user();
                Xendit::setApiKey($xendit_api);
                $params = [
                    'external_id' => $orderID,
                    'payer_email' => $student->email,
                    'description' => 'Payment for order ' . $orderID,
                    'amount' => $total_price,
                    'callback_url' =>  route('store.xendit.status'),
                    'success_redirect_url' => route('store.xendit.status', $response),
                ];

                $Xenditinvoice = \Xendit\Invoice::create($params);
                Session::put('invoicepay',$Xenditinvoice);
                return redirect($Xenditinvoice['invoice_url']);

            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e));
        }
    }

    public function storeXenditPaymentStatus(Request $request)
    {
        $data = $request->all();
        $fixedData = [];
        foreach ($data as $key => $value) {
            $fixedKey = str_replace('amp;', '', $key);
            $fixedData[$fixedKey] = $value;
        }
        $store = Store::where('slug', $fixedData['slug'])->first();

        $session = Session::get('invoicepay');
        $payment_setting = Utility::getPaymentSetting($store->id);
        $xendit_api = $payment_setting['xendit_api'];
        Xendit::setApiKey($xendit_api);
        $getInvoice = \Xendit\Invoice::retrieve($session['id']);

        $cart = session()->get($store->slug);
        if (isset($cart['coupon'])) {
            $coupon = $cart['coupon']['coupon'];
        }
        $products       = $cart['products'];
        $product_id = [];
        $sub_totalprice = 0;
        $total_price    = 0;

        foreach ($products as $key => $product) {
            $product_name[] = $product['product_name'];
            $product_id[]   = $product['id'];
            $sub_totalprice += $product['price'];
            $total_price    += $product['price'];
        }
        if (!empty($coupon)) {
            if ($coupon['enable_flat'] == 'off') {
                $discount_value = ($sub_totalprice / 100) * $coupon['discount'];
                $total_price     = $sub_totalprice - $discount_value;
            } else {
                $discount_value = $coupon['flat_discount'];
                $total_price     = $sub_totalprice - $discount_value;
            }
        }

        if ($products) {
            try {
                if($getInvoice['status'] == 'PAID'){

                    $student               = Auth::guard('students')->user();
                            $order                 = new Order();
                            $order->order_id       = $fixedData['orderId'];
                            $order->name           = $student->name;
                            $order->card_number    = '';
                            $order->card_exp_month = '';
                            $order->card_exp_year  = '';
                            $order->student_id     = $student->id;
                            $order->course         = json_encode($products);
                            $order->price          = $total_price;
                            $order->coupon         = !empty($cart['coupon']['coupon']['id']) ? $cart['coupon']['coupon']['id'] : '';
                            $order->coupon_json    = json_encode(!empty($coupon) ? $coupon : '');
                            $order->discount_price = !empty($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                            $order->price_currency = $store->currency_code;
                            $order->txn_id         = '';
                            $order->payment_type   = __('Xendit');
                            $order->payment_status =$getInvoice['status'];
                            $order->receipt        = '';
                            $order->store_id       = $store['id'];
                            $order->save();

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

                            $uArr = [
                                'order_id' =>  $fixedData['orderId'],
                                'store_name'  => $store['name'],
                            ];

                            session()->forget($request->slug);

                            // slack //
                            $settings  = Utility::notifications($store->id);
                            if (isset($settings['order_notification']) && $settings['order_notification'] == 1) {
                                Utility::send_slack_msg('new_order',$uArr,$store->created_by);
                            }

                            // telegram //
                            $settings  = Utility::notifications($store->id);
                            if (isset($settings['telegram_order_notification']) && $settings['telegram_order_notification'] == 1) {
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
                                    $msg =  redirect()->route(
                                        'store-complete.complete', [
                                                                    $store->slug,
                                                                    Crypt::encrypt($order->id),
                                                                ]
                                    )->with('success', __('Transaction has been success'));
                                } else {
                                    return redirect()->back()->with('error', __('Webhook call failed.'));
                                }
                            }

                            session()->forget($store->slug);

                            $msg =  redirect()->route(
                                'store-complete.complete', [
                                                            $store->slug,
                                                            Crypt::encrypt($order->id),
                                                        ]
                            )->with('success', __('Transaction has been success'));
                            return $msg;
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __($e->getMessage()));
            }
        }else {
            return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
        }
    }
}
