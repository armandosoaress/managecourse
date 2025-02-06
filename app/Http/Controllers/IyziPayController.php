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
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class IyziPayController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $planID    = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser  = Auth::user();
        $adminPaymentSettings = Utility::getAdminPaymentSetting();
        $iyzipay_key = $adminPaymentSettings['iyzipay_public_key'];
        $iyzipay_secret = $adminPaymentSettings['iyzipay_secret_key'];
        $iyzipay_mode = $adminPaymentSettings['iyzipay_mode'];
        $currency = $adminPaymentSettings['currency'];
        $plan = Plan::find($planID);
        $coupon_id = '0';
        $price = $plan->price;
        $coupon_code = null;
        $discount_value = null;
        $coupons = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
        if ($coupons) {
            $coupon_code = $coupons->code;
            $usedCoupun     = $coupons->used_coupon();
            if ($coupons->limit == $usedCoupun) {
                $res_data['error'] = __('This coupon code has expired.');
            } else {
                $discount_value = ($plan->price / 100) * $coupons->discount;
                $price  = $price - $discount_value;
                if ($price < 0) {
                    $price = $plan->price;
                }
                $coupon_id = $coupons->id;
            }
        }
        $res_data['total_price'] = $price;
        $res_data['coupon']      = $coupon_id;
        // set your Iyzico API credentials
        try {
            $setBaseUrl = ($iyzipay_mode == 'sandbox') ? 'https://sandbox-api.iyzipay.com' : 'https://api.iyzipay.com';
            $options = new \Iyzipay\Options();
            $options->setApiKey($iyzipay_key);
            $options->setSecretKey($iyzipay_secret);
            $options->setBaseUrl($setBaseUrl); // or "https://api.iyzipay.com" for production
            $ipAddress = Http::get('https://ipinfo.io/?callback=')->json();
            $address = ($authuser->address) ? $authuser->address : 'Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1';
            // create a new payment request
            $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
            $request->setLocale('en');
            $request->setPrice($res_data['total_price']);
            $request->setPaidPrice($res_data['total_price']);
            $request->setCurrency($currency);
            $request->setCallbackUrl(route('iyzipay.payment.callback',[$plan->id,$price,$coupon_code]));
            $request->setEnabledInstallments(array(1));
            $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
            $buyer = new \Iyzipay\Model\Buyer();
            $buyer->setId($authuser->id);
            $buyer->setName(explode(' ', $authuser->name)[0]);
            $buyer->setSurname(explode(' ', $authuser->name)[0]);
            $buyer->setGsmNumber("+" . $authuser->dial_code . $authuser->phone);
            $buyer->setEmail($authuser->email);
            $buyer->setIdentityNumber(rand(0, 999999));
            $buyer->setLastLoginDate("2023-03-05 12:43:35");
            $buyer->setRegistrationDate("2023-04-21 15:12:09");
            $buyer->setRegistrationAddress($address);
            $buyer->setIp($ipAddress['ip']);
            $buyer->setCity($ipAddress['city']);
            $buyer->setCountry($ipAddress['country']);
            $buyer->setZipCode($ipAddress['postal']);
            $request->setBuyer($buyer);
            $shippingAddress = new \Iyzipay\Model\Address();
            $shippingAddress->setContactName($authuser->name);
            $shippingAddress->setCity($ipAddress['city']);
            $shippingAddress->setCountry($ipAddress['country']);
            $shippingAddress->setAddress($address);
            $shippingAddress->setZipCode($ipAddress['postal']);
            $request->setShippingAddress($shippingAddress);
            $billingAddress = new \Iyzipay\Model\Address();
            $billingAddress->setContactName($authuser->name);
            $billingAddress->setCity($ipAddress['city']);
            $billingAddress->setCountry($ipAddress['country']);
            $billingAddress->setAddress($address);
            $billingAddress->setZipCode($ipAddress['postal']);
            $request->setBillingAddress($billingAddress);
            $basketItems = array();
            $firstBasketItem = new \Iyzipay\Model\BasketItem();
            $firstBasketItem->setId("BI101");
            $firstBasketItem->setName("Binocular");
            $firstBasketItem->setCategory1("Collectibles");
            $firstBasketItem->setCategory2("Accessories");
            $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
            $firstBasketItem->setPrice($res_data['total_price']);
            $basketItems[0] = $firstBasketItem;
            $request->setBasketItems($basketItems);

            $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);
            if($checkoutFormInitialize->getpaymentPageUrl() != null)
            {
                return redirect()->to($checkoutFormInitialize->getpaymentPageUrl());
            }else{
                return redirect()->route('plans.index')->with('error', 'Something went wrong, Please try again');
            }
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('errors', $e->getMessage());
        }
    }

    public function iyzipayCallback(Request $request,$planID,$price,$coupanCode = null)
    {
        $adminPaymentSettings = Utility::getAdminPaymentSetting();
        $plan = Plan::find($planID);
        $user = Auth::user();
        $order = new PlanOrder();
        $order->order_id = time();
        $order->name = $user->name;
        $order->card_number = '';
        $order->card_exp_month = '';
        $order->card_exp_year = '';
        $order->plan_name = $plan->name;
        $order->plan_id = $plan->id;
        $order->price = $price;
        $order->price_currency =!empty($adminPaymentSettings['currency']) ? $adminPaymentSettings['currency'] : 'USD';
        $order->txn_id = time();
        $order->payment_type = __('Iyzipay');
        $order->payment_status = 'success';
        $order->txn_id = '';
        $order->receipt = '';
        $order->user_id = $user->id;
        $order->save();
        $user = User::find($user->id);
        $coupons = Coupon::where('code', $coupanCode)->where('is_active', '1')->first();
        if (!empty($coupons)) {
            $userCoupon         = new UserCoupon();
            $userCoupon->user   = $user->id;
            $userCoupon->coupon = $coupons->id;
            $userCoupon->order  = $order->order_id;
            $userCoupon->save();
            $usedCoupun = $coupons->used_coupon();
            if ($coupons->limit <= $usedCoupun) {
                $coupons->is_active = 0;
                $coupons->save();
            }
        }
        $assignPlan = $user->assignPlan($plan->id);


        if ($assignPlan['is_success']) {
            return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
        } else {
            return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
        }
    }


    public function iyzipaypayment(Request $request, $slug)
    {
        try {
            $cart = session()->get($slug);
            $products = $cart['products'];
            $store = Store::where('slug', $slug)->first();

            $storePaymentSetting = Utility::getPaymentSetting($store->id);
            $iyzipay_public_key = $storePaymentSetting['iyzipay_public_key'];
            $iyzipay_secret_key = $storePaymentSetting['iyzipay_secret_key'];
            $iyzipay_mode = $storePaymentSetting['iyzipay_mode'];
            $currency = $store->currency_code;

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

                $coupon_id = null;
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
                $setBaseUrl = ($iyzipay_mode == 'sandbox') ? 'https://sandbox-api.iyzipay.com' : 'https://api.iyzipay.com';
                $options = new \Iyzipay\Options();
                $options->setApiKey($iyzipay_public_key);
                $options->setSecretKey($iyzipay_secret_key);
                $options->setBaseUrl($setBaseUrl); // or "https://api.iyzipay.com" for production
                $ipAddress = Http::get('https://ipinfo.io/?callback=')->json();
                $address = 'Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1';
                // create a new payment request
                $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
                $request->setLocale('en');
                $request->setPrice($get_amount);
                $request->setPaidPrice($get_amount);
                $request->setCurrency($currency);
                $request->setCallbackUrl(route('iyzipay.callback',[$slug, $get_amount]));
                $request->setEnabledInstallments(array(1));
                $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
                $buyer = new \Iyzipay\Model\Buyer();
                $buyer->setId(!empty($student->id) ? $student->id : '0');
                $buyer->setName(!empty($student->name) ? $student->name : 'Student');
                $buyer->setSurname(!empty($student->name) ? $student->name : 'Student');
                $buyer->setGsmNumber("+" . !empty($student->phone) ? $student->phone : '9999999999');
                $buyer->setEmail(!empty($student->email) ? $student->email : 'test@gmail.com');
                $buyer->setIdentityNumber(rand(0, 999999));
                $buyer->setLastLoginDate("2023-03-05 12:43:35");
                $buyer->setRegistrationDate("2023-04-21 15:12:09");
                $buyer->setRegistrationAddress($address);
                $buyer->setIp($ipAddress['ip']);
                $buyer->setCity($ipAddress['city']);
                $buyer->setCountry($ipAddress['country']);
                $buyer->setZipCode($ipAddress['postal']);
                $request->setBuyer($buyer);
                $shippingAddress = new \Iyzipay\Model\Address();
                $shippingAddress->setContactName(!empty($student->name) ? $student->name : 'Student');
                $shippingAddress->setCity($ipAddress['city']);
                $shippingAddress->setCountry($ipAddress['country']);
                $shippingAddress->setAddress($address);
                $shippingAddress->setZipCode($ipAddress['postal']);
                $request->setShippingAddress($shippingAddress);
                $billingAddress = new \Iyzipay\Model\Address();
                $billingAddress->setContactName(!empty($student->name) ? $student->name : 'Student');
                $billingAddress->setCity($ipAddress['city']);
                $billingAddress->setCountry($ipAddress['country']);
                $billingAddress->setAddress($address);
                $billingAddress->setZipCode($ipAddress['postal']);
                $request->setBillingAddress($billingAddress);
                $basketItems = array();
                $firstBasketItem = new \Iyzipay\Model\BasketItem();
                $firstBasketItem->setId("BI101");
                $firstBasketItem->setName("Binocular");
                $firstBasketItem->setCategory1("Collectibles");
                $firstBasketItem->setCategory2("Accessories");
                $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
                $firstBasketItem->setPrice($get_amount);
                $basketItems[0] = $firstBasketItem;
                $request->setBasketItems($basketItems);

                $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);
                if($checkoutFormInitialize->getpaymentPageUrl() != null)
                {
                    return redirect()->to($checkoutFormInitialize->getpaymentPageUrl());
                }else{
                    return redirect()->back()->with('error', 'Something went wrong, Please try again');
                }
            } else {
                return redirect()->back()->with('error', __('product is not found.'));
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }

    public function iyzipaypaymentCallback(Request $request ,$slug, $get_amount)
    {
        $cart     = session()->get($slug);
        $store        = Store::where('slug', $slug)->first();
        if(!empty($cart))
        {
            $products = $cart['products'];
        }
        else
        {
            return redirect()->back()->with('error', __('Please add to Course into cart'));
        }

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
                $student                = Auth::guard('students')->user();
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
                $order->txn_id          = time();;
                $order->payment_type    = 'Iyzipay';
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
            }catch(\Exception $e){
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
        else{
            return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
        }
    }
}
