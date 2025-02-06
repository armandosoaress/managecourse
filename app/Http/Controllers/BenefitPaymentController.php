<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\UserCoupon;
use App\Models\ProductCoupon;
use App\Models\PurchasedCourse;
use App\Models\Store;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class BenefitPaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $secret_key = $admin_payment_setting['benefit_secret_key'];
        $objUser = Auth::user();
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);

        if ($plan) {
            $get_amount = $plan->price;
            try {
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
                            $authuser = \Auth::user();
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
                                        'price_currency' => !empty($admin_payment_setting['currency']) ? $admin_payment_setting['currency'] : 'USD',
                                        'txn_id' => '',
                                        'payment_type' => 'Benefit',
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
                $coupon = (empty($request->coupon)) ? "0" : $request->coupon;
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $userData =
                    [
                        "amount" => $get_amount,
                        "currency" => !empty($admin_payment_setting['currency']) ? $admin_payment_setting['currency'] : 'USD',
                        "customer_initiated" => true,
                        "threeDSecure" => true,
                        "save_card" => false,
                        "description" => " Plan - " . $plan->name,
                        "metadata" => ["udf1" => "Metadata 1"],
                        "reference" => ["transaction" => "txn_01", "order" => "ord_01"],
                        "receipt" => ["email" => true, "sms" => true],
                        "customer" => ["first_name" => $objUser->name, "middle_name" => "", "last_name" => "", "email" => $objUser->email, "phone" => ["country_code" => 965, "number" => 51234567]],
                        "source" => ["id" => "src_bh.benefit"],
                        "post" => ["url" => "https://webhook.site/fd8b0712-d70a-4280-8d6f-9f14407b3bbd"],
                        "redirect" => ["url" => route('benefit.call_back', ['plan_id' => $plan->id, 'amount' => $get_amount, 'coupon' => $coupon])],


                    ];
                $responseData = json_encode($userData);
                $client = new Client();
                try {
                    $response = $client->request('POST', 'https://api.tap.company/v2/charges', [
                        'body' => $responseData,
                        'headers' => [
                            'Authorization' => 'Bearer ' . $secret_key,
                            'accept' => 'application/json',
                            'content-type' => 'application/json',
                        ],
                    ]);
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error','Currency Not Supported.Contact To Your Site Admin');
                }

                $data = $response->getBody();
                $res = json_decode($data);
                return redirect($res->transaction->url);
            } catch (Exception $e) {

                return redirect()->back()->with('error', $e);
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function call_back(Request $request)
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $secret_key = $admin_payment_setting['benefit_secret_key'];
        $user = \Auth::user();
        $plan = Plan::find($request->plan_id);
        $couponCode = $request->coupon;
        $getAmount = $request->amount;
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        if ($couponCode != 0) {
            $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
            $request['coupon_id'] = $coupons->id;
        } else {
            $coupons = null;
        }
        try {
            $post = $request->all();
            $client = new Client();
            $response = $client->request('GET', 'https://api.tap.company/v2/charges/' . $post['tap_id'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $secret_key,
                    'accept' => 'application/json',
                ],
            ]);

            $json = $response->getBody();
            $data = json_decode($json);
            $status_code = $data->gateway->response->code;

            if ($status_code == '00') {
                $order = new PlanOrder();
                $order->order_id = $orderID;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $getAmount;
                $order->price_currency = !empty($admin_payment_setting['currency']) ? $admin_payment_setting['currency'] : 'USD';
                $order->payment_type = __('Benefit');
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
    public function storeInitiatePayment(Request $request,$slug)
    {
        try {
            $cart = session()->get($slug);
            $products = $cart['products'];
            $store = Store::where('slug', $slug)->first();

            $companyPaymentSetting = Utility::getPaymentSetting($store->id);
            $secret_key = $companyPaymentSetting['benefit_secret_key'];

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
                $studentData =
                    [
                        "amount" => $get_amount,
                        "currency" => !empty($store->currency_code) ? $store->currency_code : 'USD',
                        "customer_initiated" => true,
                        "threeDSecure" => true,
                        "save_card" => false,
                        "description" => $product['product_name'],
                        "metadata" => ["udf1" => "Metadata 1"],
                        "reference" => ["transaction" => "txn_01", "order" => "ord_01"],
                        "receipt" => ["email" => true, "sms" => true],
                        "customer" => ["first_name" => $student->name, "middle_name" => "", "last_name" => "", "email" => $student->email, "phone" => ["country_code" => 965, "number" => 51234567]],
                        "source" => ["id" => "src_bh.benefit"],
                        "post" => ["url" => "https://webhook.site/fd8b0712-d70a-4280-8d6f-9f14407b3bbd"],
                        "redirect" => ["url" => route('store.benefit.call_back', ['product_id' => $product_id, 'amount' => $get_amount,$slug])],

                    ];

                $responseData = json_encode($studentData);
                $client = new Client();
                try {
                    $response = $client->request('POST', 'https://api.tap.company/v2/charges', [
                        'body' => $responseData,
                        'headers' => [
                            'Authorization' => 'Bearer ' . $secret_key,
                            'accept' => 'application/json',
                            'content-type' => 'application/json',
                        ],
                    ]);
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error','Currency Not Supported.Contact To Your Site Admin');
                }

                $data = $response->getBody();
                $res = json_decode($data);
                return redirect($res->transaction->url);

            } else {
                return redirect()->back()->with('error', __('product is not found.'));
            }

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }
    public function storeCall_back(Request $request,$slug)
    {
        $getAmount = $request->amount;
        $product_id = $request->product_id;
        $store = Store::where('slug', $slug)->first();

        $companyPaymentSetting = Utility::getPaymentSetting($store->id);
        $secret_key = $companyPaymentSetting['benefit_secret_key'];
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

            $post = $request->all();
            $client = new Client();
            $response = $client->request('GET', 'https://api.tap.company/v2/charges/' . $post['tap_id'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $secret_key,
                    'accept' => 'application/json',
                ],
            ]);

            $json = $response->getBody();
            $data = json_decode($json);
            $status_code = $data->gateway->response->code;
            if ($status_code == '00') {
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
                $order->payment_type    = 'Benefit';
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
                        $msg = 'Webhook call failed.';
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
            else{
                return redirect()->back()->with('error', __('Your Transaction is fail please try again'));
            }
        }catch(Exception $e){
            return redirect()->back()->with('error', __($e));
        }
    }
}
