<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Course;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\PurchasedCourse;
use App\Models\Store;
use App\Models\UserCoupon;
use App\Models\Student;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;

use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    // private $_api_context;
    protected $invoiceData;


    public function paymentConfig($slug = '')
    {
        if (Auth::check() && Auth::guard('students')->check() == false) {
            $payment_setting = Utility::getAdminPaymentSetting();
        } else {
            $payment_setting = Utility::getPaymentSetting(\Auth::user()->creatorId());
        }
        config(
            [
                'paypal.sandbox.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.sandbox.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]
        );
    }

    public function PayWithPaypal(Request $request, $slug)
    {
        $cart     = session()->get($slug);
        $products = $cart['products'];

        $store = Store::where('slug', $slug)->first();

        $admin_payments_details = Utility::getPaymentSetting($store->id);

        config(
            [
                'paypal.sandbox.client_id' => isset($admin_payments_details['paypal_client_id']) ? $admin_payments_details['paypal_client_id'] : '',
                'paypal.sandbox.client_secret' => isset($admin_payments_details['paypal_secret_key']) ? $admin_payments_details['paypal_secret_key'] : '',
                'paypal.mode' => isset($admin_payments_details['paypal_mode']) ? $admin_payments_details['paypal_mode'] : '',
            ]
        );

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        Session::put('paypal_payment_id', $paypalToken['access_token']);
        $objUser = Auth::user();

        $total_price    = 0;
        $sub_tax        = 0;
        $sub_totalprice = 0;
        $total_tax      = 0;
        $product_name   = [];
        $product_id     = [];

        foreach ($products as $key => $product) {
            $product_name[] = $product['product_name'];
            $product_id[]   = $product['id'];
            $sub_totalprice += $product['price'];
            $total_price    += $product['price'];
        }

        if ($products) {
            try {
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

                $response = $provider->createOrder([
                    "intent" => "CAPTURE",
                    "application_context" => [
                        "return_url" => route('get.payment.status', $store->slug),
                        "cancel_url" => route('get.payment.status', $store->slug),
                    ],
                    "purchase_units" => [
                        0 => [
                            "amount" => [
                                "currency_code" => $store->currency_code,
                                "value" => $total_price,
                            ],
                        ],
                    ],
                ]);
                if (isset($response['id']) && $response['id'] != null) {
                    // redirect to approve href
                    foreach ($response['links'] as $links) {
                        if ($links['rel'] == 'approve') {
                            return redirect()->away($links['href']);
                        }
                    }
                    return redirect()
                        ->route('plans.index')
                        ->with('error', 'Something went wrong.');
                } else {
                    return redirect()
                        ->route('plans.index')
                        ->with('error', $response['message'] ?? 'Something went wrong.');
                }
                Session::put('paypal_payment_id', $paypalToken->id);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Unknown error occurred'));
            }
        } else {
            return redirect()->back()->with('error', __('is deleted.'));
        }
    }

    public function GetPaymentStatus(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->first();
        $admin_payments_details = Utility::getPaymentSetting($store->id);

        config(
            [
                'paypal.sandbox.client_id' => isset($admin_payments_details['paypal_client_id']) ? $admin_payments_details['paypal_client_id'] : '',
                'paypal.sandbox.client_secret' => isset($admin_payments_details['paypal_secret_key']) ? $admin_payments_details['paypal_secret_key'] : '',
                'paypal.mode' => isset($admin_payments_details['paypal_mode']) ? $admin_payments_details['paypal_mode'] : '',
            ]
        );

        $cart = session()->get($slug);
        if (isset($cart['coupon'])) {
            $coupon = $cart['coupon']['coupon'];
        }
        $products       = $cart['products'];
        $store          = Store::where('slug', $slug)->first();
        $sub_totalprice = 0;
        $product_name   = [];
        $product_id     = [];

        foreach ($products as $key => $product) {
            $course = Course::where('id', $product['product_id'])->where('store_id', $store->id)->first();
            $product_name[] = $product['product_name'];
            $product_id[]   = $product['id'];
            $sub_totalprice += $product['price'];
        }
        if (!empty($coupon)) {
            if ($coupon['enable_flat'] == 'off') {
                $discount_value = ($sub_totalprice / 100) * $coupon['discount'];
                $totalprice     = $sub_totalprice - $discount_value;
            } else {
                $discount_value = $coupon['flat_discount'];
                $totalprice     = $sub_totalprice - $discount_value;
            }
        }
        if ($products) {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);
            $payment_id = Session::get('paypal_payment_id');
            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

            try {
                $order       = new Order();
                $latestOrder = Order::orderBy('created_at', 'DESC')->first();
                if (!empty($latestOrder)) {
                    $order->order_nr = '#' . str_pad($latestOrder->id + 1, 4, "100", STR_PAD_LEFT);
                } else {
                    $order->order_nr = '#' . str_pad(1, 4, "100", STR_PAD_LEFT);
                }
                $orderID = $order->order_nr;

                $statuses = '';
                if (isset($response['status']) && $response['status'] == 'COMPLETED')
                {
                    if ($response['status'] == 'COMPLETED') {
                        $statuses = __('successful');
                    }

                    $student               = Auth::guard('students')->user();
                    $order                 = new Order();
                    $order->order_id       = $orderID;
                    $order->name           = $student->name;
                    $order->card_number    = '';
                    $order->card_exp_month = '';
                    $order->card_exp_year  = '';
                    $order->student_id     = $student->id;
                    $order->course         = json_encode($products);
                    $order->price          = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
                    $order->coupon         = !empty($cart['coupon']['coupon']['id']) ? $cart['coupon']['coupon']['id'] : '';
                    $order->coupon_json    = json_encode(!empty($coupon) ? $coupon : '');
                    $order->discount_price = !empty($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                    $order->price_currency = $store->currency_code;
                    $order->txn_id         = $payment_id;
                    $order->payment_type   = __('PAYPAL');
                    $order->payment_status = $statuses;
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

                    session()->forget($slug);

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
                        'store-complete.complete',
                        [
                            $store->slug,
                            Crypt::encrypt($order->id),
                        ]
                    )->with('success', __('Transaction has been') .' '. $statuses);
                } else {
                    return redirect()->back()->with('error', __('Transaction has been') .' '.$statuses);
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Transaction has been failed.'));
            }
        } else {
            return redirect()->back()->with('error', __('is deleted.'));
        }
    }

    public function planPayWithPaypal(Request $request)
    {
        $this->paymentConfig();

        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan   = Plan::find($planID);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $payment_setting = Utility::getAdminPaymentSetting();
        $coupons_id = '';
        if ($plan) {
            try {
                $coupon_id = null;
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
                        $coupon_id = $coupons->id;
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }

                $paypalToken = $provider->getAccessToken();
                $response = $provider->createOrder([
                    "intent" => "CAPTURE",
                    "application_context" => [
                        "return_url" => route('plan.get.payment.status', [$plan->id, $price]),
                        "cancel_url" =>  route('plan.get.payment.status', [$plan->id, $price]),
                    ],
                    "purchase_units" => [
                        0 => [
                            "amount" => [
                                "currency_code" => $payment_setting['currency'],
                                "value" => $price
                            ]
                        ]
                    ]
                ]);
                if (isset($response['id']) && $response['id'] != null) {
                    // redirect to approve href
                    foreach ($response['links'] as $links) {
                        if ($links['rel'] == 'approve') {
                            return redirect()->away($links['href']);
                        }
                    }
                    return redirect()
                        ->route('plans.index')
                        ->with('error', 'Something went wrong. OR Unknown error occurred');
                } else {
                    return redirect()
                        ->route('plans.index')
                        ->with('error', $response['message'] ?? 'Something went wrong.');
                }
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetPaymentStatus(Request $request, $plan_id, $amount)
    {
        $this->paymentconfig();
        $user = Auth::user();
        $plan = Plan::find($plan_id);
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        if ($plan) {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);
            $payment_id = Session::get('paypal_payment_id');
            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

            if ($request->has('coupon_id') && $request->coupon_id != '') {
                $coupons = Coupon::find($request->coupon_id);
                if (!empty($coupons)) {
                    $userCoupon = new UserCoupon();
                    $userCoupon->user = $user->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->order = $order_id;
                    $userCoupon->save();
                    $usedCoupun = $coupons->used_coupon();
                    if ($coupons->limit <= $usedCoupun) {
                        $coupons->is_active = 0;
                        $coupons->save();
                    }
                }
            }
            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                if ($response['status'] == 'COMPLETED') {
                    $statuses = 'success';
                }
                $order = new PlanOrder();
                $order->order_id = $order_id;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $amount;
                $order->price_currency = $admin_payment_setting['currency'];
                $order->txn_id = $payment_id;
                $order->payment_type = __('PAYPAL');
                $order->payment_status = $statuses;
                $order->txn_id = '';
                $order->receipt = '';
                $order->user_id = $user->id;
                $order->save();
                $assignPlan = $user->assignPlan($plan->id);
                if ($assignPlan['is_success']) {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                } else {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }

                return redirect()
                    ->route('plans.index')
                    ->with('success', 'Transaction complete.');
            } else {
                return redirect()
                    ->route('plans.index')
                    ->with('error', $response['message'] ?? 'Something went wrong.');
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

}
