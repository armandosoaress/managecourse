<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Utility;
use App\Models\Course;
use App\Models\Order;
use App\Models\ProductCoupon;
use App\Models\PurchasedCourse;
use App\Models\Student;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Exception;

class ToyyibpayController extends Controller
{
    public  $callBackUrl, $returnUrl;

    public function toyyibpayPaymentPrepare(Request $request)
    {
        try {
            $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
            $plan   = Plan::find($planID);

            if ($plan) {
                $get_amount = $plan->price;

                $admin_payment_setting = Utility::getAdminPaymentSetting();
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
                $this->callBackUrl = route('plan.toyyibpay.callback', [$plan->id, $get_amount, $coupon]);
                $this->returnUrl = route('plan.toyyibpay.callback', [$plan->id, $get_amount, $coupon]);


                $user = Auth::user();
                $Date = date('d-m-Y');
                $ammount = $get_amount;
                $billName = $plan->name;
                $description = $plan->name;
                $billExpiryDays = 3;
                $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
                $billContentEmail = "Thank you for purchasing our product!";

                $some_data = array(
                    'userSecretKey' => $admin_payment_setting['toyyibpay_secret_key'],
                    'categoryCode' => $admin_payment_setting['category_code'],
                    'billName' => $billName,
                    'billDescription' => $description,
                    'billPriceSetting' => 1,
                    'billPayorInfo' => 1,
                    'billAmount' => 100 * $ammount,
                    'billReturnUrl' => $this->returnUrl,
                    'billCallbackUrl' => $this->callBackUrl,
                    'billExternalReferenceNo' => 'AFR341DFI',
                    'billTo' => $user->name,
                    'billEmail' => $user->email,
                    'billPhone' => '0000000000',
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
                curl_setopt($curl, CURLOPT_URL, 'https://toyyibpay.com/index.php/api/createBill');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
                $result = curl_exec($curl);
                $info = curl_getinfo($curl);
                curl_close($curl);
                $obj = json_decode($result);
                return redirect('https://toyyibpay.com/' . $obj[0]->BillCode);
            } else {
                return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
            }
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function toyyibpayPlanGetPayment(Request $request, $planId, $getAmount, $couponCode)
    {
        if ($couponCode != 0) {
            $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
            $request['coupon_id'] = $coupons->id;
        } else {
            $coupons = null;
        }
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $store_id = Auth::user()->current_store;
        $plan = Plan::find($planId);
        $user = Auth::user();
        // $request['status_id'] = 1;

        // 1=success, 2=pending, 3=fail
        try {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            if ($request->status_id == 3) {
                $statuses = 'Fail';
                $planorder                 = new PlanOrder();
                $planorder->order_id       = $orderID;
                $planorder->name           = $user->name;
                $planorder->card_number    = '';
                $planorder->card_exp_month = '';
                $planorder->card_exp_year  = '';
                $planorder->plan_name      = $plan->name;
                $planorder->plan_id        = $plan->id;
                $planorder->price          = $getAmount;
                $planorder->txn_id         = '-';
                $planorder->price_currency = $admin_payment_setting['currency'];
                $planorder->payment_type   = __('Toyyibpay');
                $planorder->payment_status = $statuses;
                $planorder->receipt        = '';
                $planorder->user_id        = $user->id;
                $planorder->store_id       = $store_id;
                $planorder->save();
                return redirect()->route('plans.index')->with('success', __('Your Transaction is fail please try again'));
            } else if ($request->status_id == 2) {
                $statuses = 'pandding';
                $planorder                 = new PlanOrder();
                $planorder->order_id       = $orderID;
                $planorder->name           = $user->name;
                $planorder->card_number    = '';
                $planorder->card_exp_month = '';
                $planorder->card_exp_year  = '';
                $planorder->plan_name      = $plan->name;
                $planorder->plan_id        = $plan->id;
                $planorder->price          = $getAmount;
                $planorder->txn_id         = '-';
                $planorder->price_currency = $admin_payment_setting['currency'];
                $planorder->payment_type   = __('Toyyibpay');
                $planorder->payment_status = $statuses;
                $planorder->receipt        = '';
                $planorder->user_id        = $user->id;
                $planorder->save();
                return redirect()->route('plans.index')->with('success', __('Your transaction on pandding'));
            } else if ($request->status_id == 1) {
                $statuses = 'success';
                $planorder                 = new PlanOrder();
                $planorder->order_id       = $orderID;
                $planorder->name           = $user->name;
                $planorder->card_number    = '';
                $planorder->card_exp_month = '';
                $planorder->card_exp_year  = '';
                $planorder->plan_name      = $plan->name;
                $planorder->plan_id        = $plan->id;
                $planorder->price          = $getAmount;
                $planorder->txn_id         = '-';
                $planorder->price_currency = $admin_payment_setting['currency'];
                $planorder->payment_type   = __('Toyyibpay');
                $planorder->payment_status = $statuses;
                $planorder->receipt        = '';
                $planorder->user_id        = $user->id;
                $planorder->save();
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

    public function toyyibpayPayment(Request $request,$slug)
    {
        try {
            $cart     = session()->get($slug);
            $products = $cart['products'];

            $store = Store::where('slug', $slug)->first();
            $get_amount    = 0;
            $sub_tax        = 0;
            $sub_totalprice = 0;
            $total_tax      = 0;
            $product_name   = [];
            $product_id     = [];

            foreach ($products as $key => $product) {
                $product_name[] = $product['product_name'];
                $product_id[]   = $product['id'];
                $sub_totalprice += $product['price'];
                $get_amount    += $product['price'];
            }
            $admin_payment_setting = Utility::getPaymentSetting($store->id);
            if ($products) {
                    $coupon_id = 0;
                    if (isset($cart['coupon']) && isset($cart['coupon'])) {
                        if ($cart['coupon']['coupon']['enable_flat'] == 'off') {
                            $discount_value = ($sub_totalprice / 100) * $cart['coupon']['coupon']['discount'];
                            $get_amount    = $sub_totalprice - $discount_value;
                        } else {
                            $discount_value = $cart['coupon']['coupon']['flat_discount'];
                            $get_amount    = $sub_totalprice - $discount_value;
                        }
                    }

                    $this->callBackUrl = route('toyyibpay.callback', [$store->slug, $get_amount, $coupon_id]);
                    $this->returnUrl = route('toyyibpay.callback', [$store->slug, $get_amount, $coupon_id]);

                    $product_name = implode(",",$product_name);
                    $student            = Auth::guard('students')->user();
                    $Date = date('d-m-Y');
                    $ammount = $get_amount;
                    $billName = $student->name;
                    $description = $product_name;
                    $billExpiryDays = 3;
                    $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
                    $billContentEmail = "Thank you for purchasing our product!";

                    $some_data = array(
                        'userSecretKey' => $admin_payment_setting['toyyibpay_secret_key'],
                        'categoryCode' => $admin_payment_setting['category_code'],
                        'billName' => $billName,
                        'billDescription' => $description,
                        'billPriceSetting' => 1,
                        'billPayorInfo' => 1,
                        'billAmount' => 100 * $ammount,
                        'billReturnUrl' => $this->returnUrl,
                        'billCallbackUrl' => $this->callBackUrl,
                        'billExternalReferenceNo' => 'AFR341DFI',
                        'billTo' => $student->name,
                        'billEmail' => $student->email,
                        'billPhone' => '0000000000',
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
                    curl_setopt($curl, CURLOPT_URL, 'https://toyyibpay.com/index.php/api/createBill');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
                    $result = curl_exec($curl);
                    $info = curl_getinfo($curl);
                    curl_close($curl);
                    $obj = json_decode($result);
                    return redirect('https://toyyibpay.com/' . $obj[0]->BillCode);

            } else {
                return redirect()->back()->with('error', __('is deleted.'));
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }

    public function toyyibpayGetPayment(Request $request, $slug, $getAmount, $couponCode)
    {
        $store = Store::where('slug', $slug)->first();
        $cart = session()->get($slug);
        $products       = $cart['products'];
        $sub_totalprice = 0;
        $product_name   = [];
        $product_id     = [];
        foreach ($products as $key => $product) {
            $course = Course::where('id', $product['product_id'])->where('store_id', $store->id)->first();
            $product_name[] = $product['product_name'];
            $product_id[]   = $product['id'];
            $sub_totalprice += $product['price'];
        }
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        // $request['status_id'] = 1;
        // 1=success, 2=pending, 3=fail
        try {
            $student            = Auth::guard('students')->user();
            if ($request->status_id == 3) {
                $statuses = 'Fail';
                $order                 = new Order();
                $order->order_id       = $orderID;
                $order->name           = $student->name;
                $order->card_number    = '';
                $order->card_exp_month = '';
                $order->card_exp_year  = '';
                $order->student_id     = $student->id;
                $order->course         = json_encode($products);
                $order->price          = $getAmount;
                $order->coupon         = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
                $order->coupon_json    = json_encode($couponCode);
                $order->discount_price = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                $order->price_currency = $store->currency_code;
                $order->txn_id         = '-';
                $order->payment_type   = 'ToyyibPay';
                $order->payment_status = $statuses;
                $order->receipt        = '';
                $order->store_id       = $store['id'];
                $order->save();
                return redirect()->route(
                    'store-complete.complete',
                    [
                        $store->slug,
                        Crypt::encrypt($order->id),
                    ]
                )->with('success', __('Transaction has been') .' '. $statuses);
            } else if ($request->status_id == 2) {
                $statuses = 'pandding';
                $order                 = new Order();
                $order->order_id       = $orderID;
                $order->name           = $student->name;
                $order->card_number    = '';
                $order->card_exp_month = '';
                $order->card_exp_year  = '';
                $order->student_id     = $student->id;
                $order->course         = json_encode($products);
                $order->price          = $getAmount;
                $order->coupon         = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
                $order->coupon_json    = json_encode($couponCode);
                $order->discount_price = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                $order->price_currency = $store->currency_code;
                $order->txn_id         = '-';
                $order->payment_type   = 'ToyyibPay';
                $order->payment_status = $statuses;
                $order->receipt        = '';
                $order->store_id       = $store['id'];
                $order->save();

                return redirect()->route(
                    'store-complete.complete',
                    [
                        $store->slug,
                        Crypt::encrypt($order->id),
                    ]
                )->with('success', __('Transaction has been') .' '. $statuses);
            } else if ($request->status_id == 1) {
                $statuses = 'success';
                $order                 = new Order();
                $order->order_id       = $orderID;
                $order->name           = $student->name;
                $order->card_number    = '';
                $order->card_exp_month = '';
                $order->card_exp_year  = '';
                $order->student_id     = $student->id;
                $order->course         = json_encode($products);
                $order->price          = $getAmount;
                $order->coupon         = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
                $order->coupon_json    = json_encode($couponCode);
                $order->discount_price = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                $order->price_currency = $store->currency_code;
                $order->txn_id         = '-';
                $order->payment_type   = 'ToyyibPay';
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

                session()->forget($slug);
                return redirect()->route(
                    'store-complete.complete',
                    [
                        $store->slug,
                        Crypt::encrypt($order->id),
                    ]
                )->with('success', __('Transaction has been') .' '. $statuses);

            } else {
                return redirect()->back()->with('error', __('Transaction has been'));
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', __('Transaction has been failed.'));
        }
    }
}


