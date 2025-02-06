<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\PurchasedCourse;
use App\Models\Store;
use App\Models\Student;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use YooKassa\Client;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class YooKassaController extends Controller
{
    public function planPayWithYooKassa(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';


        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser = Auth::user();
        $plan = Plan::find($planID);
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
                    $userCoupon->user = $authuser->id;
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

            try {

                if (is_int((int)$yookassa_shop_id)) {
                    $client = new Client();
                    $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $product = !empty($plan->name) ? $plan->name : 'Life time';
                    $payment = $client->createPayment(
                        array(
                            'amount' => array(
                                'value' => $get_amount,
                                'currency' => $currency,
                            ),
                            'confirmation' => array(
                                'type' => 'redirect',
                                'return_url' => route('plan.get.yookassa.status', [$plan->id, 'order_id' => $orderID, 'price' => $get_amount]),
                            ),
                            'capture' => true,
                            'description' => 'Заказ №1',
                        ),
                        uniqid('', true)
                    );

                    $authuser = Auth::user();
                    $authuser->plan = $plan->id;
                    $authuser->save();


                    if (!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '') {
                        try {
                            $authuser->cancel_subscription($authuser->id);
                        } catch (\Exception $exception) {
                            Log::debug($exception->getMessage());
                        }
                    }

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
                            'price_currency' => $currency,
                            'txn_id' => '',
                            'payment_type' => __('YooKassa'),
                            'payment_status' => 'pending',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );

                    Session::put('payment_id', $payment['id']);

                    if ($payment['confirmation']['confirmation_url'] != null) {
                        return redirect($payment['confirmation']['confirmation_url']);
                    } else {
                        return redirect()->route('plans.index')->with('error', 'Something went wrong, Please try again');
                    }

                } else {
                    return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                }
            } catch (\Throwable $th) {

                return redirect()->back()->with('error',  __($th->getMessage()));
            }
        }
    }
    public function planGetYooKassaStatus(Request $request, $planId)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';

        if (is_int((int)$yookassa_shop_id)) {
            $client = new Client();
            $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
            $paymentId = Session::get('payment_id');
            Session::forget('payment_id');
            if ($paymentId == null) {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }

            $payment = $client->getPaymentInfo($paymentId);

            if (isset($payment) && $payment->status == "succeeded") {

                $plan = Plan::find($planId);
                $user = auth()->user();
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                try {
                    $Order                 = PlanOrder::where('order_id', $request->order_id)->first();
                    $Order->payment_status = 'succeeded';
                    $Order->save();

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
                } catch (\Exception $e) {
                    return redirect()->route('plans.index')->with('error', __($e->getMessage()));
                }
            } else {
                return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
            }
        }
    }


    public function PayWithYookassa(Request $request,$slug)
    {
        $cart     = session()->get($slug);
        $products = $cart['products'];

        $store = Store::where('slug', $slug)->first();

        $payment_setting = Utility::getPaymentSetting($store->id);

        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];

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
            if ($products)
            {
                $coupon_id = null;
                if (isset($cart['coupon']) && isset($cart['coupon'])) {
                    if ($cart['coupon']['coupon']['enable_flat'] == 'off') {
                        $discount_value = ($sub_totalprice / 100) * $cart['coupon']['coupon']['discount'];
                        $total_price    = $sub_totalprice - $discount_value;
                    } else {
                        $discount_value = $cart['coupon']['coupon']['flat_discount'];
                        $total_price    = $sub_totalprice - $discount_value;
                    }
                }
                if (is_int((int)$yookassa_shop_id)) {
                    $client = new Client();
                    $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $payment = $client->createPayment(
                        array(
                            'amount' => array(
                                'value' => $total_price,
                                'currency' => $store->currency_code,
                            ),
                            'confirmation' => array(
                                'type' => 'redirect',
                                'return_url' => route('store.yookassa.status', [
                                    'slug'=>$slug,
                                    'amount'=>$total_price
                                ]),
                            ),
                            'capture' => true,
                            'description' => 'Заказ №1',
                        ),
                        uniqid('', true)
                    );

                    Session::put('store_payment_id', $payment['id']);

                    if ($payment['confirmation']['confirmation_url'] != null) {
                        return redirect($payment['confirmation']['confirmation_url']);
                    } else {
                        return redirect()->route('plans.index')->with('error', 'Something went wrong, Please try again');
                    }


                } else {
                    return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                }
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error',  __($th->getMessage()));
        }
    }
    public function storeYookassaPaymentStatus(Request $request)
    {
        $slug = $request->slug;
        $store = Store::where('slug', $slug)->first();

        $payment_setting = Utility::getPaymentSetting($store->id);
        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];

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
            try {
                if (is_int((int)$yookassa_shop_id)) {
                    $client = new Client();
                    $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
                    $paymentId = Session::get('store_payment_id');
                    $payment = $client->getPaymentInfo($paymentId);
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    Session::forget('store_payment_id');
                    if (isset($payment) && $payment->status == "succeeded") {

                        $user = auth()->user();
                        try {
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
                            $order->payment_type   = __('Yookassa');
                            $order->payment_status = $payment->status;
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
                                'order_id' => $orderID,
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

                        session()->forget($slug);

                        } catch (\Exception $e) {
                            return redirect()->back()->with('error', __($e->getMessage()));
                        }
                    } else {
                        return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                    }
                }
            } catch (\Exception $e) {
                return redirect()->route('store.cart', $slug)->with('success',$e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
        }
    }

}
