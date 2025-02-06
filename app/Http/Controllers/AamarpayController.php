<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\ProductCoupon;
use App\Models\PurchasedCourse;
use App\Models\Store;
use App\Models\Student;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AamarpayController extends Controller
{
    public function pay(Request $request)
    {
        $url = 'https://sandbox.aamarpay.com/request.php';
        $payment_setting = Utility::getAdminPaymentSetting();
        $aamarpay_store_id = $payment_setting['aamarpay_store_id'];
        $aamarpay_signature_key = $payment_setting['aamarpay_signature_key'];
        $aamarpay_description = $payment_setting['aamarpay_description'];
        $currency = !empty($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser = Auth::user();
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
                                        'price_currency' => $currency,
                                        'txn_id' => '',
                                        'payment_type' => 'Aamarpay',
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
                $fields = array(
                    'store_id' => $aamarpay_store_id,
                    //store id will be aamarpay,  contact integration@aamarpay.com for test/live id
                    'amount' => $get_amount,
                    //transaction amount
                    'payment_type' => '',
                    //no need to change
                    'currency' => $currency,
                    //currenct will be USD/BDT
                    'tran_id' => $orderID,
                    //transaction id must be unique from your end
                    'cus_name' => $authuser->name,
                    //customer name
                    'cus_email' => $authuser->email,
                    //customer email address
                    'cus_add1' => '',
                    //customer address
                    'cus_add2' => '',
                    //customer address
                    'cus_city' => '',
                    //customer city
                    'cus_state' => '',
                    //state
                    'cus_postcode' => '',
                    //postcode or zipcode
                    'cus_country' => '',
                    //country
                    'cus_phone' => '1234567890',
                    //customer phone number
                    'success_url' => route('pay.aamarpay.success', Crypt::encrypt(['response'=>'success','coupon' => $coupon, 'plan_id' => $plan->id, 'price' => $get_amount, 'order_id' => $orderID])),
                    //your success route
                    'fail_url' => route('pay.aamarpay.success', Crypt::encrypt(['response'=>'failure','coupon' => $coupon, 'plan_id' => $plan->id, 'price' => $get_amount, 'order_id' => $orderID])),
                    //your fail route
                    'cancel_url' => route('pay.aamarpay.success', Crypt::encrypt(['response'=>'cancel'])),
                    //your cancel url
                    'signature_key' => $aamarpay_signature_key,
                    'desc' => $aamarpay_description,
                ); //signature key will provided aamarpay, contact integration@aamarpay.com for test/live signature key


                $fields_string = http_build_query($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_URL, $url);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $url_forward = str_replace('"', '', stripslashes(curl_exec($ch)));
                curl_close($ch);

                $this->redirect_to_merchant($url_forward);
            } catch (\Exception $e) {

                return redirect()->back()->with('error', $e);
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }

    }

    function redirect_to_merchant($url)
    {

        $token = csrf_token();
        ?>
        <html xmlns="http://www.w3.org/1999/xhtml">

        <head>
            <script type="text/javascript">
                function closethisasap() { document.forms["redirectpost"].submit(); }
            </script>
        </head>

        <body onLoad="closethisasap();">

            <form name="redirectpost" method="post" action="<?php echo 'https://sandbox.aamarpay.com/' . $url; ?>">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
        </body>

        </html>
        <?php
        exit;
    }

    public function aamarpaysuccess($data, Request $request)
    {
        $data = Crypt::decrypt($data);
        $user = \Auth::user();
        $payment_setting = Utility::getAdminPaymentSetting();
        if ($data['response'] == "success")
        {
            $plan = Plan::find($data['plan_id']);
            $couponCode = $data['coupon'];
            $getAmount = $data['price'];
            $orderID = $data['order_id'];
            if ($couponCode != 0) {
                $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
                $request['coupon_id'] = $coupons->id;
            } else {
                $coupons = null;
            }

            $order = new PlanOrder();
            $order->order_id = $orderID;
            $order->name = $user->name;
            $order->card_number = '';
            $order->card_exp_month = '';
            $order->card_exp_year = '';
            $order->plan_name = $plan->name;
            $order->plan_id = $plan->id;
            $order->price = $getAmount;
            $order->price_currency = !empty($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
            $order->payment_type = __('Aamarpay');
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
        }
        elseif ($data['response'] == "cancel")
        {
            return redirect()->route('plans.index')->with('error', __('Your payment is cancel'));
        }
        else {
            return redirect()->route('plans.index')->with('error', __('Your Transaction is fail please try again'));
        }

    }
    public function payWithAamarpay(Request $request,$slug){
        try {
            $url = 'https://sandbox.aamarpay.com/request.php';
            $cart = session()->get($slug);
            $products = $cart['products'];
            $store = Store::where('slug', $slug)->first();
            $currency = $store->currency_code;
            $storepaymentSetting = Utility::getPaymentSetting($store->id);

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
                    $student = Auth::guard('students')->user();
                    $coupon = (empty($cart['coupon'])) ? "0" :$cart['coupon'];
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $fields = array(
                        'store_id' => $storepaymentSetting['aamarpay_store_id'],
                        //store id will be aamarpay,  contact integration@aamarpay.com for test/live id
                        'amount' => $total_price,
                        //transaction amount
                        'payment_type' => '',
                        //no need to change
                        'currency' => $currency,
                        //currenct will be USD/BDT
                        'tran_id' => $orderID,
                        //transaction id must be unique from your end
                        'cus_name' => $student['name'],
                        //customer name
                        'cus_email' => $student['email'],
                        //customer email address
                        'cus_add1' => '',
                        //customer address
                        'cus_add2' => '',
                        //customer address
                        'cus_city' => '',
                        //customer city
                        'cus_state' => '',
                        //state
                        'cus_postcode' => '',
                        //postcode or zipcode
                        'cus_country' => '',
                        //country
                        'cus_phone' => '1234567890',
                        //customer phone number
                        'success_url' => route('store.pay.aamarpay.success', Crypt::encrypt(['response'=>'success','slug' => $slug, 'product_id' => $product_id, 'price' => $total_price, 'order_id' => $orderID])),
                        //your success route
                        'fail_url' => route('store.pay.aamarpay.success', Crypt::encrypt(['response'=>'failure','slug' => $slug, 'product_id' => $product_id, 'price' => $total_price, 'order_id' => $orderID])),
                        //your fail route
                        'cancel_url' => route('store.pay.aamarpay.success', Crypt::encrypt(['response'=>'cancel'])),
                        //your cancel url
                        'signature_key' => $storepaymentSetting['aamarpay_signature_key'],
                        'desc' => $storepaymentSetting['aamarpay_description'],
                    ); //signature key will provided aamarpay, contact integration@aamarpay.com for test/live signature key

                    $fields_string = http_build_query($fields);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_VERBOSE, true);
                    curl_setopt($ch, CURLOPT_URL, $url);

                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $url_forward = str_replace('"', '', stripslashes(curl_exec($ch)));
                    curl_close($ch);
                    $this->redirect_to_merchant($url_forward);
                } catch (\Exception $e) {

                    return redirect()->back()->with('error', $e);
                }

            } else {
                return redirect()->back()->with('error', __('product is not found.'));
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }

    }
    public function storeAamarpaysuccess($data,Request $request){

        $data = Crypt::decrypt($data);
        $getAmount = $data['price'];
        $product_id = $data['product_id'];
        $slug = $data['slug'];
        $store = Store::where('slug', $slug)->first();
        if ($data['response'] == "success")
        {
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

            $student = Auth::guard('students')->user();
            $order                  = new Order();
            $order->order_id        = time();
            $order->name            = isset($student['name']) ? $student['name'] : '' ;
            $order->card_number     = '';
            $order->card_exp_month  = '';
            $order->card_exp_year   = '';
            $order->student_id      = isset($student['id']) ? $student['id'] : '';
            $order->price           = $getAmount;
            $order->coupon          = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
            $order->coupon_json     = json_encode($coupon);
            $order->discount_price  = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
            $order->course          = json_encode($products);
            $order->price_currency  = $store->currency_code;
            $order->txn_id          = isset($pay_id) ? $pay_id : '';
            $order->payment_type    = 'Aamarpay';
            $order->payment_status  = 'approved';
            $order->receipt         = '';
            $order->store_id         = $store['id'];
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
        elseif ($data['response'] == "cancel")
        {
            return redirect()->back()->with('error', __('Your payment is cancel'));
        }
        else {
            return redirect()->back()->with('error', __('Your Transaction is fail please try again'));
        }

    }
}
