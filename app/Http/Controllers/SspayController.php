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
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;

class SspayController extends Controller
{
    public $secretKey, $callBackUrl, $returnUrl, $categoryCode, $is_enabled ;

    public function __construct()
    {

        $payment_setting = Utility::getAdminPaymentSetting();

        $this->secretKey = isset($payment_setting['sspay_secret_key']) ? $payment_setting['sspay_secret_key'] : '';
        $this->categoryCode                = isset($payment_setting['sspay_category_code']) ? $payment_setting['sspay_category_code'] : '';
        $this->is_enabled          = isset($payment_setting['is_sspay_enabled']) ? $payment_setting['is_sspay_enabled'] : 'off';
        return $this;
    }

    public function SspayPaymentPrepare(Request $request)
    {
        try {
            $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
            $plan   = Plan::find($planID);

            if ($plan) {
                $get_amount = $plan->price;


                if (!empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if (!empty($coupons)) {
                        $usedCoupun     = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $get_amount          = $plan->price - $discount_value;

                        if ($coupons->limit == $usedCoupun) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }
                $coupon = (empty($request->coupon)) ? "0" : $request->coupon;
                $this->callBackUrl = route('plan.sspay.callback', [$plan->id, $get_amount, $coupon]);
                $this->returnUrl = route('plan.sspay.callback', [$plan->id, $get_amount, $coupon]);

                $Date = date('d-m-Y');
                $ammount = $get_amount;
                $billName = $plan->name;
                $description = $plan->name;
                $billExpiryDays = 3;
                $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
                $billContentEmail = "Thank you for purchasing our product!";

                $some_data = array(
                    'userSecretKey' => $this->secretKey,
                    'categoryCode' => $this->categoryCode,
                    'billName' => $billName,
                    'billDescription' => $description,
                    'billPriceSetting' => 1,
                    'billPayorInfo' => 1,
                    'billAmount' => 100 * $ammount,
                    'billReturnUrl' => $this->returnUrl,
                    'billCallbackUrl' => $this->callBackUrl,
                    'billExternalReferenceNo' => 'AFR341DFI',
                    'billTo' => Auth::user()->name,
                    'billEmail' => Auth::user()->email,
                    'billPhone' => '000000000',
                    'billSplitPayment' => 0,
                    'billSplitPaymentArgs' => '',
                    'billPaymentChannel' => '0',
                    'billContentEmail' => $billContentEmail,
                    'billChargeToCustomer' => 1,
                    'billExpiryDate' => $billExpiryDate,
                    'billExpiryDays' => $billExpiryDays
                );
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_URL, 'https://sspay.my/index.php/api/createBill');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
                $result = curl_exec($curl);
                $info = curl_getinfo($curl);
                curl_close($curl);
                $obj = json_decode($result);
                return redirect('https://sspay.my/' . $obj[0]->BillCode);
            } else {
                return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
            }
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function SspayPlanGetPayment(Request $request, $planId, $getAmount, $couponCode)
    {
        if ($couponCode != 0) {
            $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
            $request['coupon_id'] = $coupons->id;
        } else {
            $coupons = null;
        }
        $payment_setting = Utility::getAdminPaymentSetting();
        $plan = Plan::find($planId);
        $pay_id = $request->transaction_id;
        $user = auth()->user();
        // $request['status_id'] = 1;

        // 1=success, 2=pending, 3=fail
        try {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            if ($request->status_id == 3) {
                $statuses = 'Fail';
                $order                 = new PlanOrder();
                $order->order_id       = $orderID;
                $order->name           = $user->name;
                $order->card_number    = '';
                $order->card_exp_month = '';
                $order->card_exp_year  = '';
                $order->plan_name      = $plan->name;
                $order->plan_id        = $plan->id;
                $order->price          = $getAmount;
                $order->price_currency = $payment_setting['currency'];
                $order->payment_type   = __('Sspay');
                $order->payment_status = $statuses;
                $order->txn_id         = isset($pay_id) ? $pay_id : '';
                $order->receipt        = '';
                $order->user_id        = $user->id;
                $order->save();
                return redirect()->route('plans.index')->with('error', __('Your Transaction is fail please try again'));
            } else if ($request->status_id == 2) {
                $statuses = 'pandding';
                $order                 = new PlanOrder();
                $order->order_id       = $orderID;
                $order->name           = $user->name;
                $order->card_number    = '';
                $order->card_exp_month = '';
                $order->card_exp_year  = '';
                $order->plan_name      = $plan->name;
                $order->plan_id        = $plan->id;
                $order->price          = $getAmount;
                $order->price_currency = $payment_setting['currency'];
                $order->payment_type   = __('Sspay');
                $order->payment_status = $statuses;
                $order->txn_id         = isset($pay_id) ? $pay_id : '';
                $order->receipt        = '';
                $order->user_id        = $user->id;
                $order->save();
                return redirect()->route('plans.index')->with('error', __('Your transaction on pending'));
            } else if ($request->status_id == 1) {
                $statuses = 'success';
                $order                 = new PlanOrder();
                $order->order_id       = $orderID;
                $order->name           = $user->name;
                $order->card_number    = '';
                $order->card_exp_month = '';
                $order->card_exp_year  = '';
                $order->plan_name      = $plan->name;
                $order->plan_id        = $plan->id;
                $order->price          = $getAmount;
                $order->price_currency = $payment_setting['currency'];
                $order->payment_type   = __('Sspay');
                $order->payment_status = $statuses;
                $order->txn_id         = isset($pay_id) ? $pay_id : '';
                $order->receipt        = '';
                $order->user_id        = $user->id;
                $order->save();
                $assignPlan = $user->assignPlan($plan->id);
                $coupons = Coupon::find($request->coupon_id);
                if (!empty($request->coupon_id)) {
                    if (!empty($coupons)) {
                        $userCoupon         = new UserCoupon();
                        $userCoupon->user   = $user->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order  = $orderID;
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
                return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
            }
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function Sspaypayment(Request $request, $slug)
    {
        try {
            $cart     = session()->get($slug);
            $products = $cart['products'];

            $store = Store::where('slug', $slug)->first();

            if(Auth::check())
            {
                $companyPaymentSetting = Utility::getPaymentSetting();
            }
            else
            {
                $companyPaymentSetting = Utility::getPaymentSetting($store->id);
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
                if (isset($cart['coupon']) && isset($cart['coupon'])) {
                    if ($cart['coupon']['coupon']['enable_flat'] == 'off') {
                        $discount_value = ($sub_totalprice / 100) * $cart['coupon']['coupon']['discount'];
                        $get_amount    = $sub_totalprice - $discount_value;
                    } else {
                        $discount_value = $cart['coupon']['coupon']['flat_discount'];
                        $get_amount    = $sub_totalprice - $discount_value;
                    }
                }

                $this->callBackUrl = route('sspay.callback', [ $store->slug, $get_amount]);
                $this->returnUrl = route('sspay.callback', [ $store->slug, $get_amount]);

                $student               = Auth::guard('students')->user();
                $Date = date('d-m-Y');
                $billName = $product['product_name'];
                $description = $product['product_name'];
                $billExpiryDays = 3;
                $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
                $billContentEmail = "Thank you for purchasing our product!";

                $some_data = array(
                    'userSecretKey' => $companyPaymentSetting['sspay_secret_key'],
                    'categoryCode' =>$companyPaymentSetting['sspay_category_code'],
                    'billName' => $student->name,
                    'billDescription' => $description,
                    'billPriceSetting' => 1,
                    'billPayorInfo' => 1,
                    'billAmount' => 100 * $get_amount,
                    'billReturnUrl' => $this->returnUrl,
                    'billCallbackUrl' => $this->callBackUrl,
                    'billExternalReferenceNo' => 'AFR341DFI',
                    'billTo' => $student->name,
                    'billEmail' => $student->email,
                    'billPhone' => str_replace(array("+", "(", ")","-"), "", $student->phone_number),
                    'billSplitPayment' => 0,
                    'billSplitPaymentArgs' => '',
                    'billPaymentChannel' => '0',
                    'billContentEmail' => $billContentEmail,
                    'billChargeToCustomer' => 1,
                    'billExpiryDate' => $billExpiryDate,
                    'billExpiryDays' => $billExpiryDays
                );
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_URL, 'https://sspay.my/index.php/api/createBill');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
                $result = curl_exec($curl);
                $info = curl_getinfo($curl);
                curl_close($curl);
                $obj = json_decode($result);
                return redirect('https://sspay.my/' . $obj[0]->BillCode);
            } else {
                return redirect()->back()->with('error', __('product is not found.'));
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }
    public function Sspaycallpack(Request $request , $slug, $get_amount)
    {
        $pay_id = $request->transaction_id;
        $cart     = session()->get($slug);
        $store        = Store::where('slug', $slug)->first();
        $products       = $cart['products'];

        // $request['status_id'] = 1;

        if(isset($cart['coupon']['data_id']))
        {
            $coupon = ProductCoupon::where('id', $cart['coupon']['data_id'])->first();
        }
        else
        {
            $coupon = '';
        }

        if($products){
            try{
                if ($request->status_id == 3) {
                    return redirect()->back()->with('error','Your Transaction is fail please try again');
                }
                elseif($request->status_id == 2){
                    return redirect()->back()->with('error','Your Transaction is pending');
                }
                elseif($request->status_id == 1){
                    $student               = Auth::guard('students')->user();
                    $order                  = new Order();
                    $order->order_id        = time();
                    $order->name            = isset($student->name) ? $student->name : '' ;
                    $order->card_number     = '';
                    $order->card_exp_month  = '';
                    $order->card_exp_year   = '';
                    $order->student_id      = isset($student->id) ? $student->id : '';
                    $order->course          = json_encode($products);
                    $order->price           = $get_amount;
                    $order->coupon          = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
                    $order->coupon_json     = json_encode($coupon);
                    $order->discount_price  = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                    $order->price_currency  = $store->currency_code;
                    $order->txn_id          = isset($pay_id) ? $pay_id : '';
                    $order->payment_type    = 'Sspay';
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
                    if(isset($settings['order_notification']) && $settings['order_notification'] ==1){
                        Utility::send_slack_msg('new_order',$uArr,$store->created_by);
                    }

                    // telegram //
                    $settings  = Utility::notifications($store->id);
                    if(isset($settings['telegram_order_notification']) && $settings['telegram_order_notification'] ==1){
                        Utility::send_telegram_msg('new_order',$uArr,$store->created_by);
                    }

                    session()->forget($slug);

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

                    $msg = redirect()->route(
                        'store-complete.complete', [
                                                     $store->slug,
                                                     Crypt::encrypt($order->id),
                                                 ]
                    )->with('success', __('Transaction has been success'));


                    return $msg;
                }else{
                    return redirect()->route('store-payment.payment',[$slug])->with('error', __('Transaction Unsuccesfull'));
                }

            }catch(\Exception $e){
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
        else{
            return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
        }
    }
}
