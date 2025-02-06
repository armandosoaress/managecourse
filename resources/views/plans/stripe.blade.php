@extends('layouts.admin')
@php
    $dir= asset(Storage::url('uploads/plan'));
@endphp
@push('script-page')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        var type = window.location.hash.substr(1);
        $('.list-group-item').removeClass('active');
        $('.list-group-item').removeClass('text-primary');
        if (type != '') {
            $('a[href="#' + type + '"]').addClass('active').removeClass('text-primary');
        } else {
            $('.list-group-item:eq(0)').addClass('active').removeClass('text-primary');
        }

        $(document).on('click', '.list-group-item', function() {
            $('.list-group-item').removeClass('active');
            $('.list-group-item').removeClass('text-primary');
            setTimeout(() => {
                $(this).addClass('active').removeClass('text-primary');
            }, 10);
        });

        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>

    <script type="text/javascript">
        @if($plan->price > 0.0 && isset($admin_payments_details['is_stripe_enabled']) && $admin_payments_details['is_stripe_enabled']=='on')
        var stripe = Stripe('{{ $admin_payments_details['stripe_key'] }}');

        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        var style = {
            base: {
                // Add your base input styles here. For example:
                fontSize: '14px',
                color: '#32325d',
            },
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Create a token or display an error when the form is submitted.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            stripe.createToken(card).then(function (result) {
                if (result.error) {
                    $("#card-errors").html(result.error.message);
                    show_toastr('Error', result.error.message, 'error');
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }
        @endif
        function preparePayment(ele, payment) {
            var coupon = $(ele).closest('.row').find('.coupon').val();
            var amount = 0;
            $.ajax({
                url: '{{route('plan.prepare.amount')}}',
                datType: 'json',
                data: {
                    plan_id: '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}',
                    coupon: coupon
                },
                success: function (data) {

                    if (data.is_success == true) {
                        amount = data.price;
                        coupon_id = data.coupon_id;
                        $('#coupon_use_id').val(data.coupon_id);
                        if (payment == 'paystack') {
                            payWithPaystack(amount,coupon_id);
                        }
                        if (payment == 'flutterwave') {
                            payWithRave(amount);
                        }
                        if (payment == 'razorpay') {
                            payRazorPay(amount);
                        }
                        if (payment == 'mercado') {
                            payMercado(amount);
                        }
                        if (payment == 'bank_transfer') {
                            bank_transfer(amount);
                        }
                    } else {
                        show_toastr('Error', 'Paymenent request failed', 'error');
                    }

                }
            })
        }
        @if(isset($admin_payments_details['is_paystack_enabled']) && $admin_payments_details['is_paystack_enabled']=='on')
        function payWithPaystack(amount,coupon_id) {
            var paystack_callback = "{{ url('/paystack-plan') }}";
            var handler = PaystackPop.setup({
                key: '{{ $admin_payments_details['paystack_public_key']  }}',
                email: '{{Auth::user()->email}}',
                amount: amount * 100,
                currency: '{{$admin_payments_details['currency']}}',
                ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                    1
                ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                metadata: {
                    custom_fields: [{
                        display_name: "Mobile Number",
                        variable_name: "mobile_number",
                    }]
                },

                callback: function (response) {
                    window.location.href = paystack_callback + '/' + response.reference + '/' + '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}?coupon_id=' + coupon_id;
                },
                onClose: function () {
                    alert('window closed');
                }
            });
            handler.openIframe();

        }
        @endif
        @if(isset($admin_payments_details['is_flutterwave_enabled']) && $admin_payments_details['is_flutterwave_enabled']=='on')
        @php
        $setting = Utility::settings();
        @endphp
        // <!-- Flutterwave JAVASCRIPT FUNCTION -->
        function payWithRave(amount) {
            var coupon_id = $('#coupon_use_id').val();
            var API_publicKey = '{{ $admin_payments_details['flutterwave_public_key']  }}';
            var nowTim = "{{ date('d-m-Y-h-i-a') }}";
            var flutter_callback = "{{ url('/flutterwave-plan') }}";
            var x = getpaidSetup({
                PBFPubKey: API_publicKey,
                customer_email: '{{Auth::user()->email}}',
                amount: amount,
                currency: '{{$admin_payments_details['currency']}}',
                txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) + 'fluttpay_online-' +
                {{ date('Y-m-d') }},
                meta: [{
                    metaname: "payment_id",
                    metavalue: "id"
                }],
                onclose: function () {
                },
                callback: function (response) {

                    var txref = response.tx.txRef;

                    if (
                        response.tx.chargeResponseCode == "00" ||
                        response.tx.chargeResponseCode == "0"
                    ) {
                        window.location.href = flutter_callback + '/' + txref + '/' + '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}?coupon_id=' + coupon_id;
                    } else {
                        // redirect to a failure page.
                    }
                    x.close(); // use this to close the modal immediately after payment.
                }
            });
        }
        @endif
        @if(isset($admin_payments_details['is_razorpay_enabled']) && $admin_payments_details['is_razorpay_enabled']=='on')
        // <!-- Razorpay JAVASCRIPT FUNCTION -->
        @php
            $logo         =asset(Storage::url('uploads/logo/'));
            $company_logo = Utility::getValByName('company_logo');
            $setting = Utility::settings();
        @endphp
        function payRazorPay(amount) {
            var razorPay_callback = '{{url('razorpay-plan')}}';
            var totalAmount = amount * 100;
            var coupon_id = $('#coupon_use_id').val();
            var options = {
                "key": "{{ $admin_payments_details['razorpay_public_key']  }}", // your Razorpay Key Id
                "amount": totalAmount,
                "name": 'Plan',
                "currency": '{{$admin_payments_details['currency']}}',
                "description": "",
                "image": "{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo.png')}}",
                "handler": function (response) {
                    window.location.href = razorPay_callback + '/' + response.razorpay_payment_id + '/' + '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}?coupon_id=' + coupon_id;
                },
                "theme": {
                    "color": "#528FF0"
                }
            };
            var rzp1 = new Razorpay(options);
            rzp1.open();
        }
        @endif
        @if(isset($admin_payments_details['is_mercado_enabled']) && $admin_payments_details['is_mercado_enabled']=='on')
        // <!-- Mercado JAVASCRIPT FUNCTION -->
        function payMercado(amount) {
            var coupon_id = $('#coupon_use_id').val();
            var data = {
                coupon_id: coupon_id,
                total_price: amount,
                plan: {{$plan->id}},
            }
            $.ajax({
                url: '{{ route('mercadopago.prepare.plan') }}',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.status == 'success') {
                        window.location.href = data.url;
                    } else {
                        show_toastr("Error", data.error, data["status"]);
                    }
                }
            });
        }
        @endif

        $(document).ready(function () {
            $(document).on('click', '.apply-coupon', function () {
                var ele = $(this);
                // var coupon = $('#' + ele.attr('data-from') + '_coupon').val();
                var coupon = ele.closest('.row').find('.coupon').val();
                $.ajax({
                    url: '{{route('apply.coupon')}}',
                    datType: 'json',
                    data: {
                        plan_id: '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}',
                        coupon: coupon
                    },

                    success: function (data) {

                        $('.final-price').text(data.final_price);
                        $('#final_price_pay').val(data.price);
                        $('#mollie_total_price').val(data.price);
                        $('#skrill_total_price').val(data.price);
                        $('#coingate_total_price').val(data.price);

                        if(ele.closest($('#payfast-form')).length == 1){
                            get_payfast_status(data.price,coupon);
                        }

                        if (data.is_success == true) {
                            show_toastr('Success', data.message, 'success');
                        } else if (data.is_success == false) {
                            show_toastr('Error', data.message, 'error');
                        } else {
                            show_toastr('Error', 'Coupon code is required', 'error');
                        }
                    }
                })
            });
        });

        @if ($admin_payments_details['is_payfast_enabled'] == 'on' && !empty($admin_payments_details['payfast_merchant_id']) && !empty($admin_payments_details['payfast_merchant_key']))
        $(document).ready(function(){
            get_payfast_status(amount = 0,coupon = null);
        })

        function get_payfast_status(amount,coupon){
            var plan_id = $('#plan_id').val();
            var data = {
                coupon_id: coupon,
                total_price: amount,
                plan_id: plan_id,
            }
            $.ajax({
                url: '{{ route('payfast.payment') }}',
                method: 'POST',
                data : data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {

                    if (data.success == true) {
                        $('#get-payfast-inputs').append(data.inputs);

                    }else{
                        show_toastr('Error', data.inputs, 'error')
                    }
                }
            });
        }
        @endif


    </script>
@endpush
@php
    $dir= asset(Storage::url('uploads/plan'));
    $dir_payment= asset(Storage::url('uploads/payments'));
@endphp
@section('page-title')
    {{__('Order Summary')}}
@endsection
@section('title')
    {{__('Order Summary')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('plans.index')}}">{{__('Plan')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Order Summary')}}</li>
@endsection
@section('action-btn')
@endsection
@section('content')
<input type="hidden" id="coupon_use_id" name="user_coupon_id">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="sticky-top" style="top:30px">
                        <div class="card">
                            <div class="list-group list-group-flush" id="useradd-sidenav">

                                @if (isset($admin_payments_details['manually_enabled']) && $admin_payments_details['manually_enabled'] == 'on')
                                    <a href="#manually_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Manually') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @endif
                                @if (isset($admin_payments_details['enable_bank']) && $admin_payments_details['enable_bank'] == 'on')
                                    <a href="#bank_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Bank Transfer') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @endif
                                @if (isset($admin_payments_details['is_stripe_enabled']) && $admin_payments_details['is_stripe_enabled'] == 'on')
                                    <a href="#stripe_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Stripe') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @endif

                                @if (isset($admin_payments_details['is_paypal_enabled']) && $admin_payments_details['is_paypal_enabled'] == 'on')
                                    <a href="#paypal_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Paypal') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @endif

                                @if (isset($admin_payments_details['is_paystack_enabled']) && $admin_payments_details['is_paystack_enabled'] == 'on')
                                    <a href="#paystack_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Paystack') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif


                                @if (isset($admin_payments_details['is_flutterwave_enabled']) && $admin_payments_details['is_flutterwave_enabled'] == 'on')
                                    <a href="#flutterwave_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Flutterwave') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payments_details['is_razorpay_enabled']) && $admin_payments_details['is_razorpay_enabled'] == 'on')
                                    <a href="#razorpay_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Razorpay') }} <div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payments_details['is_mercado_enabled']) && $admin_payments_details['is_mercado_enabled'] == 'on')
                                    <a href="#mercado_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Mercado Pago') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payments_details['is_paytm_enabled']) && $admin_payments_details['is_paytm_enabled'] == 'on')
                                    <a href="#paytm_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Paytm') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @endif

                                @if (isset($admin_payments_details['is_mollie_enabled']) && $admin_payments_details['is_mollie_enabled'] == 'on')
                                    <a href="#mollie_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Mollie') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payments_details['is_skrill_enabled']) && $admin_payments_details['is_skrill_enabled'] == 'on')
                                    <a href="#skrill_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Skrill') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payments_details['is_coingate_enabled']) && $admin_payments_details['is_coingate_enabled'] == 'on')
                                    <a href="#coingate_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Coingate') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payments_details['is_paymentwall_enabled']) && $admin_payments_details['is_paymentwall_enabled'] == 'on')
                                    <a href="#paymentwall_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Paymentwall') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if (isset($admin_payments_details['is_toyyibpay_enabled']) && $admin_payments_details['is_toyyibpay_enabled'] == 'on')
                                    <a href="#toyyibpay_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Toyyibpay') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if (isset($admin_payments_details['is_payfast_enabled']) && $admin_payments_details['is_payfast_enabled'] == 'on')
                                    <a href="#payfast_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Payfast') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if (isset($admin_payments_details['is_iyzipay_enabled']) && $admin_payments_details['is_iyzipay_enabled'] == 'on')
                                    <a href="#iyzipay_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Iyzipay') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if (isset($admin_payments_details['is_sspay_enabled']) && $admin_payments_details['is_sspay_enabled'] == 'on')
                                    <a href="#sspay_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Sspay') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if (isset($admin_payments_details['is_paytab_enabled']) && $admin_payments_details['is_paytab_enabled'] == 'on')
                                    <a href="#paytab_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('PayTab') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if (isset($admin_payments_details['is_benefit_enabled']) && $admin_payments_details['is_benefit_enabled'] == 'on')
                                    <a href="#benefit_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Benefit') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if (isset($admin_payments_details['is_cashfree_enabled']) && $admin_payments_details['is_cashfree_enabled'] == 'on')
                                    <a href="#cashfree_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Cashfree') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if (isset($admin_payments_details['is_aamarpay_enabled']) && $admin_payments_details['is_aamarpay_enabled'] == 'on')
                                    <a href="#aamarpay_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Aamarpay') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if (isset($admin_payments_details['is_paytr_enabled']) && $admin_payments_details['is_paytr_enabled'] == 'on')
                                    <a href="#paytr_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Paytr') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if (isset($admin_payments_details['is_yookassa_enabled']) && $admin_payments_details['is_yookassa_enabled'] == 'on')
                                    <a href="#yookassa_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Yookassa') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if (isset($admin_payments_details['is_midtrans_enabled']) && $admin_payments_details['is_midtrans_enabled'] == 'on')
                                    <a href="#midtrans_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Midtrans') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if (isset($admin_payments_details['is_xendit_enabled']) && $admin_payments_details['is_xendit_enabled'] == 'on')
                                    <a href="#xendit_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Xendit') }}<div
                                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                            </div>
                        </div>

                        <div class="mt-5">
                            <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.2s" style="
                                                                            visibility: visible;
                                                                            animation-delay: 0.2s;
                                                                            animation-name: fadeInUp;
                                                                          ">
                                <div class="card-body">
                                    <span class="price-badge bg-primary">{{ $plan->name }}</span>
                                    @if (\Auth::user()->plan == $plan->id)
                                        <div class="d-flex flex-row-reverse m-0 p-0 ">
                                            <span class="d-flex align-items-center ">
                                                <i class="f-10 lh-1 fas fa-circle text-success"></i>
                                                <span class="ms-2">{{ __('Active') }}</span>
                                            </span>
                                        </div>
                                    @endif

                                    <div class="text-end">
                                        <div class="">
                                            @if (\Auth::user()->type == 'super admin')
                                                <a title="Edit Plan" data-size="lg" href="#" class="action-item"
                                                    data-url="{{ route('plans.edit', $plan->id) }}"
                                                    data-ajax-popup="true" data-title="{{ __('Edit Plan') }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Edit Plan') }}"><i class="fas fa-edit"></i></a>
                                            @endif
                                        </div>
                                    </div>

                                    <h3 class="mb-4 f-w-600  ">
                                        {{ $admin_payments_details['currency_symbol'] ? $admin_payments_details['currency_symbol'] : '$' }}{{ $plan->price . ' / ' . __(\App\Models\Plan::$arrDuration[$plan->duration]) }}</small>
                                        </h1>
                                        <p class="mb-0">
                                            {{ __('Trial : ') . $plan->trial_days . __(' Days') }}<br />
                                        </p>
                                        @if ($plan->description)
                                            <p class="mb-0">
                                                {{ $plan->description }}<br />
                                            </p>
                                        @endif
                                        <ul class="list-unstyled my-5">
                                            @if ($plan->enable_custdomain == 'on')
                                                <li>
                                                    <span class="theme-avtar">
                                                    <i class="ti ti-circle-plus text-primary"></i></span>{{ __('Custom Domain') }}
                                                </li>
                                            @else
                                                <li class="text-danger">
                                                    <span class="theme-avtar">
                                                    <i class="ti ti-circle-plus x-circle text-danger"></i></span>{{ __('Custom Domain') }}
                                                </li>
                                            @endif
                                            @if ($plan->enable_custsubdomain == 'on')
                                                <li>
                                                    <span class="theme-avtar">
                                                    <i class="ti ti-circle-plus text-primary"></i></span>{{ __('Sub Domain') }}
                                                </li>
                                            @else
                                                <li class="text-danger">
                                                        <span class="theme-avtar">
                                                    <i class="ti ti-circle-plus x-circle text-danger"></i></span>{{ __('Sub Domain') }}
                                                </li>
                                            @endif
                                            @if ($plan->additional_page == 'on')
                                                <li>
                                                    <span class="theme-avtar">
                                                        <i class="ti ti-circle-plus text-primary"></i></span>{{ __('Additional Page') }}
                                                </li>
                                            @else
                                                <li class="text-danger">
                                                    <span class="theme-avtar">
                                                    <i class="ti ti-circle-plus x-circle text-danger"></i></span>{{ __('Additional Page') }}
                                                </li>
                                            @endif
                                            @if ($plan->blog == 'on')
                                                <li>
                                                    <span class="theme-avtar">
                                                        <i class="ti ti-circle-plus text-primary"></i></span>{{ __('Blog') }}
                                                </li>
                                            @else
                                                <li class="text-danger">
                                                    <span class="theme-avtar">
                                                        <i class="ti ti-circle-plus x-circle text-danger"></i></span>{{ __('Blog') }}
                                                </li>

                                            @endif
                                        </ul>

                                        <div class="row mb-3">
                                            <div class="col-6 text-center">
                                                <span class="h5 mb-0">{{$plan->max_courses}}</span>
                                                <span class="d-block text-sm">{{__('Courses')}}</span>
                                            </div>
                                            <div class="col-6 text-center">
                                                <span class="h5 mb-0">{{$plan->max_stores}}</span>
                                                <span class="d-block text-sm"> {{__('Store')}}</span>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <!-- manually payment -->
                    @if (!empty($admin_payments_details['manually_enabled']) && $admin_payments_details['manually_enabled'] == 'on'  )
                        <div id="manually_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Manually') }}</h5>
                            </div>
                            <div class="tab-pane {{ ($admin_payments_details['manually_enabled'] == 'on') ? 'active': '' }}"
                                id="manually_payment">
                                <div class="border p-3 rounded">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <p class="mb-0 pt-1 text-sm">
                                                {{ __('Requesting manual payment for the planned amount for the subscriptions plan.') }}
                                            </p>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-sm-12 my-2 px-2">
                                    <div class="text-end">
                                        <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                            class="btn btn-primary m-1"
                                            data-title="{{ __('Send Request') }}" data-toggle="tooltip">{{ __('Send Request') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- bank transfer payment -->
                    @if (!empty($admin_payments_details['enable_bank'] ) && $admin_payments_details['enable_bank'] == 'on' && !empty($admin_payments_details['bank_number']) )
                        <div id="bank_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Bank Transfer') }}</h5>
                            </div>
                            <div class="tab-pane {{ ($admin_payments_details['enable_bank'] == 'on' && !empty($admin_payments_details['bank_number'])) ? 'active': '' }}"
                                id="bank_payment">
                            <form action="{{ route('plan.bank_transfer') }}" method="POST"
                                class="payment-method-form" id="bank_transfer_form" enctype='multipart/form-data'>
                                @csrf
                                <div class="border p-3 rounded stripe-payment-div">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label
                                                    class="form-label">{{ __('Bank Details :') }}</label>
                                                    <p class="">
                                                        {!!$admin_payments_details['bank_number'] !!}
                                                    </p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('Payment Receipt') }}</label>
                                                <input type="file" class="form-control mb-2" required name="payment_receipt" id="payment_receipt" aria-label="file example" onchange="document.getElementById('blah3').src = window.URL.createObjectURL(this.files[0])">
                                                <img src="" id="blah3" width="25%"/>
                                                <div class="invalid-feedback">{{ __('invalid form file') }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 ">
                                        <div class="row align-items-center">
                                            <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="bank_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                    <input type="text" id="bank_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="col-form-label">{{ __('Plan Price : ') }}<span
                                                        class="bank-final-price">{{ $admin_payments_details['currency_symbol'] ? $admin_payments_details['currency_symbol'] : '$' }}{{ $plan->price }}</span></label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for=""
                                                    class="col-form-label">{{ __('Net Amount : ') }}<span
                                                        class="final-price">{{ $admin_payments_details['currency_symbol'] ? $admin_payments_details['currency_symbol'] : '$' }}{{ $plan->price }}</span></label><br>
                                                <small class="text-xs">
                                                    {{ __('(After Coupon Apply)') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="col-sm-12 my-2 px-2">
                                    <div class="text-end">
                                        <input type="hidden" name="plan_id"
                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                        <input type="submit" value="{{ __('Pay Now') }}"
                                            class="btn btn-primary">
                                    </div>
                                </div>
                            </form>

                            </div>
                        </div>
                    @endif
                    <!--  bank transfer payment end -->

                    <!-- stripe payment -->
                    @if ($admin_payments_details['is_stripe_enabled'] == 'on' && !empty($admin_payments_details['stripe_key']) && !empty($admin_payments_details['stripe_secret']))
                        <div id="stripe_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Stripe') }}</h5>
                            </div>
                            <div class="tab-pane {{ ($admin_payments_details['is_stripe_enabled'] == 'on' &&!empty($admin_payments_details['stripe_key']) &&!empty($admin_payments_details['stripe_secret'])) == 'on'? 'active': '' }}"
                                id="stripe_payment">
                                <form role="form" action="{{ route('stripe.payment') }}" method="post" class="require-validation" id="payment-form">
                                    @csrf
                                    <div class="border p-3 rounded stripe-payment-div">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="custom-radio">
                                                    <label
                                                        class="font-16 font-weight-bold">{{ __('Credit / Debit Card') }}</label>
                                                </div>
                                                <p class="mb-0 pt-1 text-sm">
                                                    {{ __('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.') }}
                                                </p>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="card-name-on"
                                                        class="form-label text-dark">{{ __('Name on card') }}</label>
                                                    <input type="text" name="name" id="card-name-on"
                                                        class="form-control required"
                                                        placeholder="{{ \Auth::user()->name }}">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div id="card-element"> </div>
                                                <div id="card-errors" role="alert"></div>
                                            </div>

                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="stripe_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="stripe_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="error" style="display: none;">
                                                    <div class='alert-danger alert'>
                                                        {{ __('Please correct the errors and try again.') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <input type="submit" value="{{ __('Pay Now') }}"
                                                class="btn btn-primary">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- stripr payment end -->

                    <!-- paypal end -->
                    @if ($admin_payments_details['is_paypal_enabled'] == 'on' && !empty($admin_payments_details['paypal_client_id']) && !empty($admin_payments_details['paypal_secret_key']))
                        <div id="paypal_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Paypal') }}</h5>
                            </div>

                            <div class="tab-pane {{ ($admin_payments_details['is_stripe_enabled'] != 'on' &&$admin_payments_details['is_paypal_enabled'] == 'on' &&!empty($admin_payments_details['paypal_client_id']) &&!empty($admin_payments_details['paypal_secret_key'])) == 'on'? 'active': '' }}"
                                id="paypal_payment">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form"
                                    action="{{ route('plan.pay.with.paypal') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="paypal_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="paypal_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="submit" value="{{ __('Pay Now') }}"
                                                class="btn btn-primary">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- paypal end -->

                    <!-- Paystack -->
                    @if (isset($admin_payments_details['is_paystack_enabled']) && $admin_payments_details['is_paystack_enabled'] == 'on')
                        <div id="paystack_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Paystack') }}</h5>

                            </div>
                            <div id="paystack-payment" class="tabs-card">
                                <div class="">
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="col-12 mt-4 mb-3">
                                            <div class="row align-items-center">
                                                <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="paystack_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                        <input type="text" id="paystack_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                    <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right paymentwall-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="paymentwall-coupon-price"></b>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <button class="btn btn-primary" type="button"
                                                onclick="preparePayment(this,'paystack')">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- Paystack end -->

                    <!-- Flutterwave -->
                    @if (isset($admin_payments_details['is_flutterwave_enabled']) && $admin_payments_details['is_flutterwave_enabled'] == 'on')
                        <div id="flutterwave_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Flutterwave') }}</h5>
                            </div>
                            <div class="tab-pane " id="flutterwave_payment">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form"
                                    action="{{ route('plan.pay.with.paypal') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">

                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="flutterwave_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="flutterwave_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <button class="btn btn-primary" type="button"
                                                onclick="preparePayment(this,'flutterwave')">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Flutterwave END -->

                    <!-- Razorpay -->
                    @if (isset($admin_payments_details['is_razorpay_enabled']) && $admin_payments_details['is_razorpay_enabled'] == 'on')
                        <div id="razorpay_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Razorpay') }} </h5>

                            </div>
                            <div class="tab-pane " id="razorpay_payment">

                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form"
                                    action="{{ route('plan.pay.with.paypal') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">

                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="razorpay_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="razorpay_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <button class="btn btn-primary" type="button"
                                                onclick="preparePayment(this,'razorpay')">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Razorpay end -->

                    <!-- Mercado Pago -->
                    @if (isset($admin_payments_details['is_mercado_enabled']) && $admin_payments_details['is_mercado_enabled'] == 'on')
                        <div id="mercado_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Mercado Pago') }}</h5>

                            </div>
                            <div class="tab-pane " id="mercado_payment">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form"
                                    action="{{ route('plan.pay.with.paypal') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">

                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="mercado_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="mercado_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <button class="btn btn-primary" type="button"
                                                onclick="preparePayment(this,'mercado')">
                                                {{ __('Pay Now') }}
                                            </button>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Mercado Pago end -->

                    <!-- Paytm -->
                    @if (isset($admin_payments_details['is_paytm_enabled']) && $admin_payments_details['is_paytm_enabled'] == 'on')
                        <div id="paytm_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Paytm') }}</h5>
                            </div>
                            <div class="tab-pane " id="paytm_payment">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form"
                                    action="{{ route('paytm.prepare.plan') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                    <input type="hidden" name="total_price" id="paytm_total_price"
                                        value="{{ $plan->price }}" class="form-control">
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="mobile_number">{{ __('Mobile Number') }}</label>
                                                    <input type="text" id="mobile_number" name="mobile_number"
                                                        class="form-control coupon"
                                                        placeholder="{{ __('Enter Mobile Number') }}">
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="col-12 mt-4 mb-3">
                                                    <div class="row align-items-center">
                                                        <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                            <div class="form-group">
                                                                <label for="paytm_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                                <input type="text" id="paytm_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                            <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <button class="btn btn-primary" type="submit">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Paytm end -->

                    <!-- Mollie -->
                    @if (isset($admin_payments_details['is_mollie_enabled']) && $admin_payments_details['is_mollie_enabled'] == 'on')
                        <div id="mollie_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Mollie') }}</h5>
                            </div>
                            <div class="tab-pane " id="mollie_payment">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form"
                                    action="{{ route('mollie.prepare.plan') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                    <input type="hidden" name="total_price" id="mollie_total_price"
                                        value="{{ $plan->price }}" class="form-control">
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">

                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="mollie_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="mollie_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <button class="btn btn-primary" type="submit">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Mollie end -->

                    <!-- Skrill -->
                    @if (isset($admin_payments_details['is_skrill_enabled']) && $admin_payments_details['is_skrill_enabled'] == 'on')
                        <div id="skrill_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Skrill') }}</h5>

                            </div>
                            <div class="tab-pane " id="skrill_payment">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form"
                                    action="{{ route('skrill.prepare.plan') }}">
                                    @csrf
                                    <input type="hidden" name="id"
                                        value="{{ date('Y-m-d') }}-{{ strtotime(date('Y-m-d H:i:s')) }}-payatm">
                                    <input type="hidden" name="order_id"
                                        value="{{ str_pad(!empty($order->id) ? $order->id + 1 : 0 + 1, 4, '100', STR_PAD_LEFT) }}">
                                    @php
                                        $skrill_data = [
                                            'transaction_id' => md5(date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id'),
                                            'user_id' => 'user_id',
                                            'amount' => 'amount',
                                            'currency' => 'currency',
                                        ];
                                        session()->put('skrill_data', $skrill_data);

                                    @endphp
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                    <input type="hidden" name="total_price" id="skrill_total_price"
                                        value="{{ $plan->price }}" class="form-control">
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="skrill_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="skrill_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <button class="btn btn-primary" type="submit">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Skrill end -->

                    <!-- Coingate -->
                    @if (isset($admin_payments_details['is_coingate_enabled']) && $admin_payments_details['is_coingate_enabled'] == 'on')
                        <div id="coingate_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Coingate') }}</h5>
                            </div>
                            <div class="tab-pane " id="coingate_payment">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form"
                                    action="{{ route('coingate.prepare.plan') }}">
                                    @csrf
                                    <input type="hidden" name="counpon" id="coingate_coupon" value="">
                                    <input type="hidden" name="plan_id"
                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                    <input type="hidden" name="total_price" id="coingate_total_price"
                                        value="{{ $plan->price }}" class="form-control">
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="coingate_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="coingate_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <button class="btn btn-primary" type="submit">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Coingate end -->

                    <!-- Paymentwall -->
                    @if (isset($admin_payments_details['is_paymentwall_enabled']) && $admin_payments_details['is_paymentwall_enabled'] == 'on')
                        <div id="paymentwall_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Paymentwall') }}</h5>
                            </div>
                            <div class="tab-pane " id="paymentwall_payment">
                                <form role="form" action="{{ route('paymentwall') }}" method="post"
                                    id="paymentwall-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="paymentwall_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="paymentwall_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_paymentwall">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Paymentwall end -->

                    <!-- Toyyibpay -->
                    @if (isset($admin_payments_details['is_toyyibpay_enabled']) && $admin_payments_details['is_toyyibpay_enabled'] == 'on')
                        <div id="toyyibpay_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Toyyibpay') }}</h5>
                            </div>
                            <div class="tab-pane " id="toyyibpay_payment">
                                <form role="form" action="{{ route('toyyibpay.prepare.plan') }}" method="post"
                                    id="toyyibpay-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="toyyibpay_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="toyyibpay_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_toyyibpay">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Toyyibpay end -->

                    <!-- Payfast -->
                    @if (isset($admin_payments_details['is_payfast_enabled']) && $admin_payments_details['is_payfast_enabled'] == 'on')
                        <div id="payfast_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Payfast') }}</h5>

                            </div>
                            <div class="tab-pane {{ ($admin_payments_details['is_payfast_enabled'] == 'on' &&!empty($admin_payments_details['payfast_merchant_id']) &&!empty($admin_payments_details['payfast_merchant_key'])) == 'on'? 'active': '' }}">
                                @php
                                    $pfHost = $admin_payments_details['payfast_mode'] == 'sandbox' ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
                                @endphp
                                <form role="form" action={{"https://" . $pfHost . "/eng/process"}} method="post" class="require-validation" id="payfast-form" >
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="payfast_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="payfast_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="get-payfast-inputs"></div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id" id="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_payfast">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Payfast end -->

                    <!-- Iyzipay -->
                    @if (isset($admin_payments_details['is_iyzipay_enabled']) && $admin_payments_details['is_iyzipay_enabled'] == 'on')
                        <div id="iyzipay_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Iyzipay') }}</h5>
                            </div>
                            <div class="tab-pane " id="iyzipay_payment">
                                <form role="form" action="{{ route('iyzipay.payment.init') }}" method="post"
                                    id="iyzipay-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="iyzipay_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="iyzipay_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_iyzipay">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Iyzipay end -->

                    <!-- Sspay -->
                    @if (isset($admin_payments_details['is_sspay_enabled']) && $admin_payments_details['is_sspay_enabled'] == 'on')
                        <div id="sspay_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Sspay') }}</h5>
                            </div>
                            <div class="tab-pane " id="sspay_payment">
                                <form role="form" action="{{ route('sspay.prepare.plan') }}" method="post"
                                    id="sspay-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="sspay_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="sspay_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_sspay">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Sspay end -->

                    <!-- PayTab -->
                    @if (isset($admin_payments_details['is_paytab_enabled']) && $admin_payments_details['is_paytab_enabled'] == 'on')
                        <div id="paytab_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('payTab') }}</h5>
                            </div>
                            <div class="tab-pane " id="paytab_payment">
                                <form role="form" action="{{ route('plan.pay.with.paytab') }}" method="post"
                                    id="paytab-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="paytab_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="paytab_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_paytab">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- PayTab end -->

                    <!-- Benefit -->
                    @if (isset($admin_payments_details['is_benefit_enabled']) && $admin_payments_details['is_benefit_enabled'] == 'on')
                        <div id="benefit_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Benefit') }}</h5>
                            </div>
                            <div class="tab-pane " id="benefit_payment">
                                <form role="form" action="{{ route('benefit.initiate') }}" method="post"
                                    id="benefit-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="benefit_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="benefit_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice  btn-primary  m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_benefit">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Benefit end -->

                    <!-- Cashfree -->
                    @if (isset($admin_payments_details['is_cashfree_enabled']) && $admin_payments_details['is_cashfree_enabled'] == 'on')
                        <div id="cashfree_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Cashfree') }}</h5>
                            </div>
                            <div class="tab-pane " id="cashfree_payment">
                                <form role="form" action="{{ route('cashfree.payment') }}" method="post"
                                    id="cashfree-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="cashfree_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="cashfree_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice btn-primary m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_cashfree">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Cashfree end -->

                    <!-- Aamarpay -->
                    @if (isset($admin_payments_details['is_aamarpay_enabled']) && $admin_payments_details['is_aamarpay_enabled'] == 'on')
                        <div id="aamarpay_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Aamarpay') }}</h5>
                            </div>
                            <div class="tab-pane " id="aamarpay_payment">
                                <form role="form" action="{{ route('pay.aamarpay.payment') }}" method="post"
                                    id="aamarpay-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="aamarpay_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="aamarpay_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice btn-primary m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_aamarpay">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Aamarpay end -->

                    <!-- Paytr -->
                    @if (isset($admin_payments_details['is_paytr_enabled']) && $admin_payments_details['is_paytr_enabled'] == 'on')
                        <div id="paytr_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('PayTR') }}</h5>
                            </div>
                            <div class="tab-pane" id="paytr_payment">
                                <form role="form" action="{{ route('plan.pay.with.paytr') }}" method="post"
                                    id="paytr-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="paytr_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="paytr_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice btn-primary m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_paytr">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Paytr end -->

                    <!-- Yookassa -->
                    @if (isset($admin_payments_details['is_yookassa_enabled']) && $admin_payments_details['is_yookassa_enabled'] == 'on')
                        <div id="yookassa_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Yookassa') }}</h5>
                            </div>
                            <div class="tab-pane" id="yookassa_payment">
                                <form role="form" action="{{ route('plan.pay.with.yookassa') }}" method="post"
                                    id="yookassa-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="yookassa_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="yookassa_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice btn-primary m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_yookassa">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Yookassa end -->

                    <!-- Midtrans -->
                    @if (isset($admin_payments_details['is_midtrans_enabled']) && $admin_payments_details['is_midtrans_enabled'] == 'on')
                        <div id="midtrans_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Midtrans') }}</h5>
                            </div>
                            <div class="tab-pane" id="paytr_payment">
                                <form role="form" action="{{ route('plan.pay.with.midtrans') }}" method="post"
                                    id="midtrans-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="midtrans_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="midtrans_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice btn-primary m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_midtrans">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                    </div>
                    @endif
                    <!-- Midtrans end -->

                    <!-- Xendit -->
                    @if (isset($admin_payments_details['is_xendit_enabled']) && $admin_payments_details['is_xendit_enabled'] == 'on')
                        <div id="xendit_payment" class="card">
                            <div class="card-header">
                                <h5>{{ __('Xendit') }}</h5>
                            </div>
                            <div class="tab-pane" id="paytr_payment">
                                <form role="form" action="{{ route('plan.pay.with.xendit') }}" method="post"
                                    id="xendit-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="d-flex align-items-center">
                                            <div class="col-12 mt-4 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-10 col-mg-10 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="xendit_coupon"class="form-label">{{ __('Coupon') }}</label>
                                                            <input type="text" id="xendit_coupon" name="coupon" class="form-control coupon" placeholder="{{__('Enter Coupon Code Here')}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-mg-2 col-sm-12 col-xs-12">
                                                        <a href="#" class="btn btn-print-invoice btn-primary m-r-10 apply-coupon">{{__('Apply')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary" type="submit" id="pay_with_xendit">
                                                {{ __('Pay Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <!-- Xendit end -->
                </div>

            </div>
        </div>
    </div>
@endsection

