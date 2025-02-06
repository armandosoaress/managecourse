@extends('layouts.admin')
@section('page-title')
    {{ __('Plans') }}
@endsection

@section('title')
    {{ __('Plans') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Plans') }}</li>
@endsection
@section('action-btn')
<div class="text-end align-items-end d-flex justify-content-end">
    @if (Auth::user()->type == 'super admin')
        @if ((isset($admin_payments_setting['is_stripe_enabled']) && $admin_payments_setting['is_stripe_enabled'] == 'on') ||
            (isset($admin_payments_setting['is_paypal_enabled']) && $admin_payments_setting['is_paypal_enabled'] == 'on') ||
            (isset($admin_payments_setting['is_paystack_enabled']) && $admin_payments_setting['is_paystack_enabled'] == 'on') ||
            (isset($admin_payments_setting['is_flutterwave_enabled']) && $admin_payments_setting['is_flutterwave_enabled'] == 'on') ||
            (isset($admin_payments_setting['is_razorpay_enabled']) && $admin_payments_setting['is_razorpay_enabled'] == 'on') ||
            (isset($admin_payments_setting['is_mercado_enabled']) && $admin_payments_setting['is_mercado_enabled'] == 'on') ||
            (isset($admin_payments_setting['is_paytm_enabled']) && $admin_payments_setting['is_paytm_enabled'] == 'on') ||
            (isset($admin_payments_setting['is_mollie_enabled']) && $admin_payments_setting['is_mollie_enabled'] == 'on') ||
            (isset($admin_payments_setting['is_skrill_enabled']) && $admin_payments_setting['is_skrill_enabled'] == 'on'))
            @can('create plan')
                <div class="btn btn-sm btn-primary btn-icon m-1">
                    <a href="#" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="{{ __('Add Plan') }}" data-ajax-popup="true" data-size="lg" data-title="{{ __('Add Plan') }}"
                        data-url="{{ route('plans.create') }}"><i class="ti ti-plus text-white"></i></a>
                </div>
            @endcan

        @endif
    @endif
</div>
@endsection
@section('content')
    <div class="row plan_cards_row">
        @foreach ($plans as $plan)
            <div class="col-lg-4 col-xl-3 col-md-6 col-sm-6">
                <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.2s" style="visibility: visible;
                        animation-delay: 0.2s;
                        animation-name: fadeInUp; ">
                    <div class="card-body">
                        <span class="price-badge bg-primary">{{ $plan->name }}</span>
                        @if (\Auth::user()->type != 'super admin' && \Auth::user()->plan == $plan->id)
                            <div class="d-flex flex-row-reverse m-0 p-0">
                                <span class="d-flex align-items-center">
                                    <i class="f-10 lh-1 fas fa-circle text-success"></i>
                                    <span class="ms-2">{{ __('Active') }}</span>
                                </span>
                            </div>
                        @endif

                        @if (\Auth::user()->type == 'super admin')
                            @can('edit plan')
                                <div class="text-end ms-2">
                                    <a href="#" class="mx-3 btn btn-sm align-items-center action-btn bg-primary" data-ajax-popup="true" data-size="lg" data-title="Edit Plan" data-url="{{ route('plans.edit', $plan->id) }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ __('Edit') }}" title=""><span class="text-white"><i class="ti ti-edit"></i></span></a>
                                </div>
                            @endcan
                        @endif

                        <h3 class="mb-4 f-w-600"> {{!empty($admin_payments_setting['currency_symbol'])?$admin_payments_setting['currency_symbol']:'$'}}{{$plan->price.' / ' . __($plan->duration)}}</h3>

                        @if ($plan->description)
                            <p class="mb-0">
                                {{ $plan->description }}<br/>
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
                            @if ($plan->enable_chatgpt == 'on')
                                <li>
                                    <span class="theme-avtar">
                                        <i class="ti ti-circle-plus text-primary"></i></span>{{ __('Chatgpt') }}
                                </li>
                            @else
                                <li class="text-danger">
                                    <span class="theme-avtar">
                                        <i class="ti ti-circle-plus x-circle text-danger"></i></span>{{ __('Chatgpt') }}
                                </li>
                            @endif
                        </ul>

                        <div class="row mb-3">
                            <div class="col-3 text-center">
                                @if($plan->max_courses == '-1')
                                    <span class="text-sm h6">{{__('Unlimited')}}</span>
                                @else
                                    <span class="h5 mb-0">{{$plan->max_courses}}</span>
                                @endif
                                <span class="d-block text-sm">{{__('Courses')}}</span>
                            </div>
                            <div class="col-3 text-center">
                                @if($plan->max_users == '-1')
                                    <span class="text-sm h6">{{__('Unlimited')}}</span>
                                @else
                                    <span class="h5 mb-0">{{$plan->max_users}}</span>
                                @endif
                                <span class="d-block text-sm">{{__('Users')}}</span>
                            </div>
                            <div class="col-3 text-center">
                                @if($plan->max_stores == '-1')
                                    <span class="text-sm h6">{{__('Unlimited')}}</span>
                                @else
                                    <span class="h5 mb-0">{{$plan->max_stores}}</span>
                                @endif
                                <span class="d-block text-sm">{{__('Store')}}</span>
                            </div>
                            <div class="col-3 text-center">
                                @if($plan->storage_limit == '-1')
                                    <span class="text-sm h6">{{__('Unlimited')}}</span>
                                @else
                                    <span class="h5 mb-0">{{$plan->storage_limit}}</span>
                                @endif
                                <span class="d-block text-sm">{{__('Storage limit')}}</span>
                            </div>
                        </div>
                        <div class="row">
                            @if (\Auth::user()->type != 'super admin')
                                @if(    \Auth::user()->plan == $plan->id)
                                    <h5 class="h6">
                                        {{__('Expired : ')}} {{\Auth::user()->plan_expire_date ? Utility::dateFormat(\Auth::user()->plan_expire_date):__('lifetime')}}
                                    </h5>
                                @elseif(\Auth::user()->plan == $plan->id && !empty(\Auth::user()->plan_expire_date) && \Auth::user()->plan_expire_date < date('Y-m-d'))
                                    <div class="col-12">
                                        <p class="server-plan font-weight-bold text-center mx-sm-5">
                                            {{ __('Expired') }}
                                        </p>
                                    </div>
                                @else
                                    @if(!$plan->price == 0)
                                        <div class="{{ $plan->id == 1 ? 'col-12' : 'col-8' }}">
                                            <a href="{{ route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                                class="btn btn-primary d-flex justify-content-center align-items-center">{{ __('Subscribe') }}
                                                <i class="fas fa-arrow-right m-1"></i></a>
                                            <p></p>
                                        </div>
                                    @endif
                                @endif
                            @endif

                            @if($plan->id != 1 && \Auth::user()->type!='super admin' && \Auth::user()->plan != $plan->id)
                                @if(\Auth::user()->requested_plan != $plan->id)
                                    <div class="col-4">
                                        <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                            class="btn btn-primary btn-icon m-1"
                                            data-title="{{ __('Send Request') }}" data-toggle="tooltip">
                                            <span class="btn-inner--icon"><i class="fas fa-share"></i></span>
                                        </a>
                                    </div>
                                @else
                                    <div class="col-4">
                                        <a href="{{ route('request.cancel', \Auth::user()->id) }}"
                                            class="btn btn-icon m-1 btn-danger"
                                            data-title="{{ __('Cancle Request') }}" data-toggle="tooltip">
                                            <span class="btn-inner--icon"><i class="fas fa-times"></i></span>
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="pc-dt-simple" class="table mb-0 dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name"> {{ __('Order Id') }}</th>
                                    <th scope="col" class="sort" data-sort="budget">{{ __('Date') }}</th>
                                    <th scope="col" class="sort" data-sort="status">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Plan Name') }}</th>
                                    <th scope="col" class="sort" data-sort="completion"> {{ __('Price') }}</th>
                                    <th scope="col" class="sort" data-sort="completion"> {{ __('Payment Type') }}</th>
                                    <th scope="col" class="sort" data-sort="completion"> {{ __('Status') }}</th>
                                    <th scope="col" class="sort" data-sort="completion"> {{ __('Coupon') }}</th>
                                    <th scope="col" class="sort" data-sort="completion"> {{ __('Invoice') }}</th>
                                    @if(\Auth::user()->type == 'super admin')
                                        <th scope="col" class="sort" data-sort="completion"> {{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_id }}</td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td>{{ $order->user_name }}</td>
                                        <td>{{ $order->plan_name }}</td>
                                        <td>{{ !empty($admin_payments_setting['currency_symbol'])?$admin_payments_setting['currency_symbol']:'$' . $order->price }}</td>
                                        <td>{{ $order->payment_type }}</td>
                                        <td>
                                            @if ($order->payment_status == 'succeeded')
                                                <i class="mdi mdi-circle text-success"></i> {{ ucfirst($order->payment_status) }}
                                            @else
                                                <i class="mdi mdi-circle text-danger"></i> {{ ucfirst($order->payment_status) }}
                                            @endif
                                        </td>
                                        <td>{{ !empty($order->total_coupon_used)? (!empty($order->total_coupon_used->coupon_detail)? $order->total_coupon_used->coupon_detail->code: '-'): '-' }}
                                        </td>
                                        <td class="text-center">
                                            @if ($order->receipt != 'free coupon' && $order->payment_type == 'STRIPE')
                                                <a href="{{ $order->receipt }}" title="Invoice" target="_blank"
                                                    class=""><i class="fas fa-file-invoice"></i> </a>
                                            @elseif ($order->payment_type == 'Bank Transfer')
                                                <a href="{{ \App\Models\Utility::get_file($order->receipt) }}"  title="Invoice" target="_blank"
                                                    class="">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                            @elseif($order->receipt == 'free coupon')
                                                <p>{{ __('Used 100 % discount coupon code.') }}</p>
                                            @elseif($order->payment_type == 'Manually')
                                                <p>{{ __('Manually plan upgraded by super admin') }}</p>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @if(\Auth::user()->type == 'super admin')
                                        <td>
                                            @if($order->payment_status == 'pending' && $order->payment_type == 'Bank Transfer')
                                                <div class="action-btn bg-warning ms-2">
                                                    <a  class="mx-3 btn btn-sm  align-items-center"
                                                        data-url="{{ route('bank_transfer.show',$order->id) }}"
                                                        data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title=""
                                                        data-title="{{ __('Payment Status') }}"
                                                        data-bs-original-title="{{ __('Payment Status') }}">
                                                        <i class="ti ti-caret-right text-white"></i>
                                                    </a>
                                                </div>

                                            @endif
                                            <div class="action-btn bg-danger ms-2">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['planorder.destroy', $order->id]]) !!}
                                                    <a href="#!" class="mx-3 btn btn-sm align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                {!! Form::close() !!}
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var tohref = '';
            @if (Auth::user()->is_register_trial == 1)
                tohref = $('#trial_{{ Auth::user()->interested_plan_id }}').attr("href");
            @elseif(Auth::user()->interested_plan_id != 0)
                tohref = $('#interested_plan_{{ Auth::user()->interested_plan_id }}').attr("href");
            @endif

            if (tohref != '') {
                window.location = tohref;
            }

        });
    </script>
@endpush
