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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function planPayWithMidtrans(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $midtrans_secret = $payment_setting['midtrans_secret'];
        $mode = $payment_setting['midtrans_mode'];
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        try{

            $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        }
        catch(\Throwable $th){
            return redirect()->route('plans.index')->with('error',$th->getMessage());
        }
        $plan = Plan::find($planID);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        if ($plan) {
            $get_amount = round($plan->price);

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
            // Set your Merchant Server Key
            \Midtrans\Config::$serverKey = $midtrans_secret;
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            if($mode == 'sandbox')
            {
                \Midtrans\Config::$isProduction = false;
            }
            else
            {
                \Midtrans\Config::$isProduction = true;
            }
            // Set sanitization on (default)
            \Midtrans\Config::$isSanitized = true;
            // Set 3DS transaction for credit card to true
            \Midtrans\Config::$is3ds = true;

            $params = array(
                'transaction_details' => array(
                    'order_id' => $orderID,
                    'gross_amount' => $get_amount,
                ),
                'customer_details' => array(
                    'first_name' => Auth::user()->name,
                    'last_name' => '',
                    'email' => Auth::user()->email,
                    'phone' => '8787878787',
                ),
            );
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $authuser = Auth::user();
            $authuser->plan = $plan->id;
            $authuser->save();

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
                    'payment_type' => __('Midtrans'),
                    'payment_status' => 'pending',
                    'receipt' => null,
                    'user_id' => $authuser->id,
                ]
            );
            $data = [
                'snap_token' => $snapToken,
                'midtrans_secret' => $midtrans_secret,
                'order_id' => $orderID,
                'plan_id' => $plan->id,
                'amount' => $get_amount,
                'fallback_url' => 'plan.get.midtrans.status',
                'mode' => $mode
            ];

            return view('midtras.payment', compact('data'));
        }
    }

    public function planGetMidtransStatus(Request $request)
    {
        $response = json_decode($request->json, true);
        if (isset($response['status_code']) && $response['status_code'] == 200) {
            $plan = Plan::find($request['plan_id']);
            $user = auth()->user();
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            try {
                $Order                 = PlanOrder::where('order_id', $request['order_id'])->first();
                $Order->payment_status = 'succeeded';
                $Order->save();

                $assignPlan = $user->assignPlan($plan->id);

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
            return redirect()->back()->with('error', $response['status_message']);
        }
    }

    public function PayWithMidtrans(Request $request,$slug)
    {
        $cart     = session()->get($slug);
        $products = $cart['products'];

        $store = Store::where('slug', $slug)->first();

        $payment_setting = Utility::getPaymentSetting($store->id);

        $midtrans_secret = $payment_setting['midtrans_secret'];
        $mode = $payment_setting['midtrans_mode'];
        $get_amount = round($request->amount);
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
                 // Set your Merchant Server Key
                \Midtrans\Config::$serverKey = $midtrans_secret;
                // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
                if($mode == 'sandbox')
                {
                    \Midtrans\Config::$isProduction = false;
                }
                else
                {
                    \Midtrans\Config::$isProduction = true;
                }
                // Set sanitization on (default)
                \Midtrans\Config::$isSanitized = true;
                // Set 3DS transaction for credit card to true
                \Midtrans\Config::$is3ds = true;
                $student               = Auth::guard('students')->user();
                $params = array(
                    'transaction_details' => array(
                        'order_id' => $orderID,
                        'gross_amount' => $total_price,
                    ),
                    'customer_details' => array(
                        'first_name' => $student->name,
                        'last_name' => '',
                        'email' => $student->email,
                        'phone' => '8787878787',
                    ),
                );
                $snapToken = \Midtrans\Snap::getSnapToken($params);


                $data = [
                    'snap_token' => $snapToken,
                    'midtrans_secret' => $midtrans_secret,
                    'slug'=>$slug,
                    'amount'=>$total_price,
                    'fallback_url' => 'store.midtrans.status',
                    'mode' => $mode
                ];

                return view('midtras.payment', compact('data'));
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e));
        }
    }

    public function storeMidtransPaymentStatus(Request $request)
    {
        $slug = $request->slug;
        $store = Store::where('slug', $slug)->first();

        $response = json_decode($request->json, true);
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
        if ($products) {
            try {
                if (isset($response['status_code']) && $response['status_code'] == 200)
                {
                    $student               = Auth::guard('students')->user();
                    $order                 = new Order();
                    $order->order_id       = $response['order_id'];
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
                    $order->payment_type   = __('Midtrans');
                    $order->payment_status = 'success';
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
                        'order_id' =>  $response['order_id'],
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

                    session()->forget($slug);

                    $msg =  redirect()->route(
                        'store-complete.complete', [
                                                    $store->slug,
                                                    Crypt::encrypt($order->id),
                                                ]
                    )->with('success', __('Transaction has been success'));
                    return $msg;
                }else{
                    return redirect()->back()->with('error', $response['status_message']);
                }

            } catch (\Exception $e) {
                return redirect()->back()->with('error', __($e->getMessage()));
            }
        }else {
            return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
        }
    }

}
