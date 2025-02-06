<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Course;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\ProductCoupon;
use App\Models\PurchasedCourse;
use App\Models\Student;
use App\Models\PlanOrder;
use App\Models\Plan;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;

class PayfastController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            $payment_setting = Utility::getAdminPaymentSetting();

            $planID = Crypt::decrypt($request->plan_id);
            $plan = Plan::find($planID);
            if ($plan) {

                $plan_amount = $plan->price;
                $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                $user = Auth::user();
                if ($request->coupon_amount > 0 && $request->coupon_code != null) {
                    $coupons = Coupon::where('code', $request->coupon_code)->first();
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
                    $coupon_amount = str_replace(",","",$request->coupon_amount);
                        $plan_amount = $plan_amount - $coupon_amount;
                    }


                }
                $success = Crypt::encrypt([
                    'plan' => $plan->toArray(),
                    'order_id' => $order_id,
                    'plan_amount' => $plan_amount
                ]);
                $data = array(
                    // Merchant details
                    'merchant_id' => !empty($payment_setting['payfast_merchant_id']) ? $payment_setting['payfast_merchant_id'] : '',
                    'merchant_key' => !empty($payment_setting['payfast_merchant_key']) ? $payment_setting['payfast_merchant_key'] : '',
                    'return_url' => route('payfast.payment.success',$success),
                    'cancel_url' => route('plans.index'),
                    'notify_url' => route('plans.index'),
                    // Buyer details
                    'name_first' => $user->name,
                    'name_last' => '',
                    'email_address' => $user->email,
                    // Transaction details
                    'm_payment_id' => $order_id, //Unique payment ID to pass through to notify_url
                    'amount' => number_format(sprintf('%.2f', $plan_amount), 2, '.', ''),
                    'item_name' => $plan->name,
                );

                $passphrase = !empty($payment_setting['payfast_signature']) ? $payment_setting['payfast_signature'] : '';
                $signature = $this->generateSignature($data, $passphrase);
                $data['signature'] = $signature;

                $htmlForm = '';

                foreach ($data as $name => $value) {
                    $htmlForm .= '<input name="' . $name . '" type="hidden" value=\'' . $value . '\' />';
                }
                return response()->json([
                    'success' => true,
                    'inputs' => $htmlForm,
                ]);

            }
        }

    }
    public function generateSignature($data, $passPhrase = null)
    {
        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($val !== '') {
                $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }

        $getString = substr($pfOutput, 0, -1);
        if ($passPhrase !== null) {
            $getString .= '&passphrase=' . urlencode(trim($passPhrase));
        }
        return md5($getString);
    }

    public function success($success)
    {
        $adminPaymentSettings = Utility::getAdminPaymentSetting();
        try{
            $user = Auth::user();
            $data = Crypt::decrypt($success);
            $order = new PlanOrder();
            $order->order_id = $data['order_id'];
            $order->name = $user->name;
            $order->card_number = '';
            $order->card_exp_month = '';
            $order->card_exp_year = '';
            $order->plan_name = $data['plan']['name'];
            $order->plan_id = $data['plan']['id'];
            $order->price = $data['plan_amount'];
            $order->price_currency = !empty($adminPaymentSettings['currency']) ? $adminPaymentSettings['currency'] : 'USD';;
            $order->txn_id = $data['order_id'];
            $order->payment_type = __('PayFast');
            $order->payment_status = 'success';
            $order->txn_id = '';
            $order->receipt = '';
            $order->user_id = $user->id;
            $order->save();
            $assignPlan = $user->assignPlan($data['plan']['id']);

            if ($assignPlan['is_success']) {
                return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
            } else {
                return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
            }
        }catch(\Exception $e){
            return redirect()->route('plans.index')->with('error', __($e));
        }
    }

    // store payment

    public function payfastPayment(Request $request,$slug)
    {

            $cart     = session()->get($slug);
            $products = $cart['products'];

            $store = Store::where('slug', $slug)->first();

            if(Auth::check())
            {
                $payment_setting = Utility::getPaymentSetting();
            }
            else
            {
                $payment_setting = Utility::getPaymentSetting($store->id);
            }

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

                $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                $student =  Auth::guard('students')->user();

                if (isset($cart['coupon']) && isset($cart['coupon'])) {
                    if ($cart['coupon']['coupon']['enable_flat'] == 'off') {
                        $discount_value = ($sub_totalprice / 100) * $cart['coupon']['coupon']['discount'];
                        $get_amount    = $sub_totalprice - $discount_value;
                    } else {
                        $discount_value = $cart['coupon']['coupon']['flat_discount'];
                        $get_amount    = $sub_totalprice - $discount_value;
                    }
                }
                $success = Crypt::encrypt([
                    'order_id' => $order_id,
                    'get_amount' => $get_amount
                ]);
                $data = array(
                    // Merchant details
                    'merchant_id' => !empty($payment_setting['payfast_merchant_id']) ? $payment_setting['payfast_merchant_id'] : '',
                    'merchant_key' => !empty($payment_setting['payfast_merchant_key']) ? $payment_setting['payfast_merchant_key'] : '',
                    'return_url' => route('payfast.success',[$success,$store->slug]),
                    'cancel_url' => route('store.slug',[$store->slug]),
                    'notify_url' => route('store.slug',[$store->slug]),
                    // Buyer details
                    'name_first' => $student->name,
                    'name_last' => '',
                    'email_address' => $student->email,
                    // Transaction details
                    'm_payment_id' => $order_id, //Unique payment ID to pass through to notify_url
                    'amount' => number_format(sprintf('%.2f', $get_amount), 2, '.', ''),
                    'item_name' => $product['product_name'],
                );
                $passphrase = !empty($payment_setting['payfast_signature']) ? $payment_setting['payfast_signature'] : '';
                $signature = $this->generateSignature($data, $passphrase);
                $data['signature'] = $signature;

                $htmlForm = '';

                foreach ($data as $name => $value) {
                    $htmlForm .= '<input name="' . $name . '" type="hidden" value=\'' . $value . '\' />';
                }
                return response()->json([
                    'success' => true,
                    'inputs' => $htmlForm,
                ]);

            }

    }

    public function payfastsuccess($success,$slug)
    {
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

            $student = Auth::guard('students')->user();
            $data = Crypt::decrypt($success);
            $order = new Order();
            $order->order_id = $data['order_id'];
            $order->name = $student->name;
            $order->card_number = '';
            $order->card_exp_month = '';
            $order->card_exp_year = '';
            $order->student_id     = $student->id;
            $order->course         = json_encode($products);
            $order->price          = $data['get_amount'];
            $order->coupon         = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
            $order->coupon_json    = json_encode($coupon);
            $order->discount_price = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
            $order->price_currency = $store->currency_code;
            $order->txn_id         = '-';
            $order->payment_type   = 'Payfast';
            $order->payment_status = 'success';
            $order->receipt        = '';
            $order->store_id       = $store['id'];
            $order->save();


            foreach($products as $course_id)
            {
                $purchased_course = new PurchasedCourse();
                $purchased_course->course_id  = $course_id['product_id'];
                $purchased_course->student_id = $student->id;
                $purchased_course->order_id   = $order->id;
                $purchased_course->save();

                $student=Student::where('id',$purchased_course->student_id)->first();
                $student->courses_id=$purchased_course->course_id;
                $student->save();
            }

            session()->forget($slug);

            $uArr = [
                'order_id' => $data['order_id'],
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
        }catch(\Exception $e){
            return redirect()->back()->with('error', __($e));
        }
    }
}
