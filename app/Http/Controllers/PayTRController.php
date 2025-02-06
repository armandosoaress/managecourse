<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Course;
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
use Illuminate\Http\Request;

class PayTRController extends Controller
{
    public function planPayWithPaytr(Request $request)
    {
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan   = Plan::find($planID);
        $user = \Auth::user();

        $payment_setting = Utility::getAdminPaymentSetting();
        $paytr_merchant_id = $payment_setting['paytr_merchant_id'];
        $paytr_merchant_key = $payment_setting['paytr_merchant_key'];
        $paytr_merchant_salt = $payment_setting['paytr_merchant_salt'];
        $currency =  !empty($payment_setting['currency']) ? $payment_setting['currency'] : 'TL';

        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        if ($plan) {

            $price     = $plan->price;
            if (!empty($request->coupon)) {
                $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun     = $coupons->used_coupon();
                    $discount_value = ($plan->price / 100) * $coupons->discount;
                    $price          = $plan->price - $discount_value;
                    if ($coupons->limit == $usedCoupun) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                    if ($price <= 0) {
                        $user->plan = $plan->id;
                        $user->save();
                        $assignPlan = $user->assignPlan($plan->id);
                        if ($assignPlan['is_success'] == true && !empty($plan)) {
                            if (!empty($user->payment_subscription_id) && $user->payment_subscription_id != '') {
                                try {
                                    $user->cancel_subscription($user->id);
                                } catch (\Exception $exception) {
                                    \Log::debug($exception->getMessage());
                                }
                            }
                            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                            $userCoupon = new UserCoupon();

                            $userCoupon->user = $user->id;
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
                                    'price' => $price == null ? 0 : $price,
                                    'price_currency' => $currency,
                                    'txn_id' => '',
                                    'payment_type' => 'PayTR',
                                    'payment_status' => 'success',
                                    'receipt' => null,
                                    'user_id' => $user->id,
                                ]
                            );
                            $assignPlan = $user->assignPlan($plan->id);
                            return redirect()->route('plans.index')->with('success', __('Plan Successfully Activated'));
                        }
                    }
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }
            try {

                $email = Auth::user()->email;
                $user_name = Auth::user()->name;
                $user_address = 'no address';
                $user_phone = '0000000000';

                $user_basket = base64_encode(json_encode(array(
                    array("Plan", $price, 1),
                )));

                if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                    $ip = $_SERVER["HTTP_CLIENT_IP"];
                } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                } else {
                    $ip = $_SERVER["REMOTE_ADDR"];
                }

                $timeout_limit = "30";
                $debug_on = 1;
                $test_mode = 0;
                $no_installment = 0;
                $max_installment = 0;
                $lang =($user->lang == 'tr') ? 'tr' : 'en';

                # For 14.45 TL, 14.45 * 100 = 1445 (must be multiplied by 100 and sent as an integer.)
                $paytr_price = $price * 100;
                $hash_str = $paytr_merchant_id . $ip . $orderID . $email . $paytr_price . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
                $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $paytr_merchant_salt, $paytr_merchant_key, true));

                $request['orderID'] = $orderID;
                $request['plan_id'] = $plan->id;
                $request['price'] = $price;
                $request['payment_status'] = 'failed';
                $payment_failed = $request->all();
                $request['payment_status'] = 'success';
                $payment_success = $request->all();
                $post_vals = array(
                    'merchant_id' => $paytr_merchant_id,
                    'user_ip' => $ip,
                    'merchant_oid' => $orderID,
                    'email' => $email,
                    'payment_amount' => $paytr_price,
                    'paytr_token' => $paytr_token,
                    'user_basket' => $user_basket,
                    'debug_on' => $debug_on,
                    'lang' => $lang,
                    'no_installment' => $no_installment,
                    'max_installment' => $max_installment,
                    'user_name' => $user_name,
                    'user_address' => $user_address,
                    'user_phone' => $user_phone,
                    'merchant_ok_url' => route('plan.get.paytr.status', $payment_success),
                    'merchant_fail_url' => route('plan.get.paytr.status', $payment_failed),
                    'timeout_limit' => $timeout_limit,
                    'currency' => $currency,
                    'test_mode' => $test_mode
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);

                $result = @curl_exec($ch);

                if (curl_errno($ch)) {
                    return redirect()->route('plans.index')->with('error', curl_error($ch));
                }
                curl_close($ch);

                $result = json_decode($result, 1);

                if ($result['status'] == 'success') {
                    $token = $result['token'];
                } else {
                    return redirect()->route('plans.index')->with('error', $result['reason']);
                }

                return view('plans.paytr_payment', compact('token'));
            } catch (\Throwable $th) {
                return redirect()->route('plans.index')->with('error', $th->getMessage());
            }
        }
    }

    public function planGetPaytrStatus(Request $request)
    {
        if ($request->payment_status == "success")
        {
            try {
                $user = \Auth::user();
                $planID = $request->plan_id;
                $plan = Plan::find($planID);
                $couponCode = $request->coupon;
                $getAmount = $request->price;

                if ($couponCode != 0) {
                    $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
                    $request['coupon_id'] = $coupons->id;
                } else {
                    $coupons = null;
                }
                $payment_setting = Utility::getAdminPaymentSetting();
                $order = new PlanOrder();
                $order->order_id = $request->orderID;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $getAmount;
                $order->price_currency = !empty($payment_setting['currency']) ? $payment_setting['currency'] : 'TL';
                $order->txn_id = $request->orderID;
                $order->payment_type = __('PayTR');
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
                        $userCoupon->order = $request->orderID;
                        $userCoupon->save();
                        $usedCoupun = $coupons->used_coupon();
                        if ($coupons->limit <= $usedCoupun) {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }
                    }
                }

                if ($assignPlan['is_success'])
                {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                } else
                {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }
            } catch (\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __($e));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('success', __('Your Transaction is fail please try again.'));
        }
    }

    public function PayWithPaytr(Request $request,$slug)
    {
        $cart = session()->get($slug);
        $products = $cart['products'];
        $store = Store::where('slug', $slug)->first();

        $storepaymentSetting = Utility::getPaymentSetting($store->id);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        $product_id = [];
        $sub_totalprice = 0;
        $total_price    = 0;

        foreach ($products as $key => $product) {
            $product_name[] = $product['product_name'];
            $product_id[]   = $product['id'];
            $sub_totalprice += $product['price'];
            $total_price    += $product['price'];
        }
        if ($products)
        {
            if (isset($cart['coupon']) && isset($cart['coupon'])) {
                if ($cart['coupon']['coupon']['enable_flat'] == 'off') {
                    $discount_value = ($sub_totalprice / 100) * $cart['coupon']['coupon']['discount'];
                    $total_price    = $sub_totalprice - $discount_value;
                } else {
                    $discount_value = $cart['coupon']['coupon']['flat_discount'];
                    $total_price    = $sub_totalprice - $discount_value;
                }
            }

            try {

                $merchant_id    = $storepaymentSetting['paytr_merchant_id'];
                $merchant_key   = $storepaymentSetting['paytr_merchant_key'];
                $merchant_salt  = $storepaymentSetting['paytr_merchant_salt'];

                $student = Auth::guard('students')->user();
                $email = $student->email;

                $user_name = $student->name;

                $user_address = 'no address';
                $user_phone =  '0000000000';

                $user_basket = base64_encode(json_encode(array(
                    array("Course", $total_price, 1),
                )));

                if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                    $ip = $_SERVER["HTTP_CLIENT_IP"];
                } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                } else {
                    $ip = $_SERVER["REMOTE_ADDR"];
                }

                $timeout_limit = "30";
                $debug_on = 1;
                $test_mode = 0;
                $no_installment = 0;
                $max_installment = 0;
                $currency = $store->currency_code;
                $lang = 'tr';

                # For 14.45 TL, 14.45 * 100 = 1445 (must be multiplied by 100 and sent as an integer.)
                $paytr_price = $total_price * 100;
                $hash_str = $merchant_id . $ip . $orderID . $email . $paytr_price . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
                $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

                $request['slug'] = $slug;
                $request['payment_status'] = 'failed';
                $payment_failed = $request->all();
                $request['payment_status'] = 'success';
                $payment_success = $request->all();
                $post_vals = array(
                    'merchant_id' => $merchant_id,
                    'user_ip' => $ip,
                    'merchant_oid' => $orderID,
                    'email' => $email,
                    'payment_amount' => $paytr_price,
                    'paytr_token' => $paytr_token,
                    'user_basket' => $user_basket,
                    'debug_on' => $debug_on,
                    'lang' => $lang,
                    'no_installment' => $no_installment,
                    'max_installment' => $max_installment,
                    'user_name' => $user_name,
                    'user_address' => $user_address,
                    'user_phone' => $user_phone,
                    'merchant_ok_url' => route('store.paytrpayment.status',$payment_success),
                    'merchant_fail_url' => route('store.paytrpayment.status',$payment_failed),
                    'timeout_limit' => $timeout_limit,
                    'currency' => $currency,
                    'test_mode' => $test_mode
                );


                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);

                $result = @curl_exec($ch);

                if (curl_errno($ch))
                {
                    return redirect()->route('store.cart', $store->slug)->with('error', curl_error($ch));
                }
                curl_close($ch);

                $result = json_decode($result, 1);
                if ($result['status'] == 'success') {
                    $token = $result['token'];
                } else {
                    return redirect()->back()->with('error', 'Currency Not Supported.Contact To Your Site Admin');

                }

                return view('storefront.paytr_payment', compact('token'));
            } catch (\Throwable $th) {

                return redirect()->back()->with('error', __($th->getMessage()));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function storePaytrPaymentStatus(Request $request)
    {
        $slug = $request->slug;
        $store = Store::where('slug', $slug)->first();
        $cart = session()->get($slug);
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
        if ($products)
        {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            try
            {
                if ($request->payment_status == "success")
                {
                    $student               = Auth::guard('students')->user();
                    $order                 = new Order();
                    $order->order_id       = $orderID;
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
                    $order->payment_type   = __('PayTR');
                    $order->payment_status = $request->payment_status;
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
                        'order_id' => $order->order_id,
                        'store_name'  => $store['name'],
                    ];
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
                            $msg = redirect()->route(
                                'store-complete.complete', [
                                                             $store->slug,
                                                             Crypt::encrypt($order->id),
                                                         ]
                            )->with('success', __('Transaction has been success'));
                        } else {
                            $msg = redirect()->route(
                                'store-complete.complete', [
                                                            $store->slug,
                                                            Crypt::encrypt($order->id),
                                                        ]
                            )->with('success', __('Webhook call failed.'));
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

                }
                elseif ($request->payment_status == "cancel")
                {
                    return redirect()->back()->with('error', __('Your payment is cancel'));
                }
                else
                {
                    return redirect()->route('store.cart', $slug)->with('error', __('Transaction fail'));
                }

            }
            catch (\Exception $e)
            {
                return redirect()->route('store.cart', $slug)->with('success',$e->getMessage());
            }
        } else {
            return redirect()->route('store.cart', $slug)->with('success', __('Invoice not found.'));
        }

    }
}
