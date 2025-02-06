<div class="row">
    <div class="col-lg-12">
        <table class="table  modal-table">
            <tr>
                <td class="h6">{{__('Order Id')}}</td>
                <td>{{ $order->order_id }}</td>
            </tr>
            <tr>
                <td class="h6">{{__('Amount')}}</td>
                <td>{{ $order->price }}</td>
            </tr>
            <tr>
                <td class="h6">{{__('Payment Type')}}</td>
                <td>{{ $order->payment_type }}</td>
            </tr>
            <tr>
                <td class="h6">{{__('Payment Status')}}</td>
                <td>{{ $order->payment_status }}</td>
            </tr>
            <tr>
                <td class="h6">{{__('Bank Details')}}</td>
                <td>{!! !empty($store_settings['bank_number'])?$store_settings['bank_number']:'' !!}</td>
            </tr>
            <tr>
                <td class="h6">{{__('Payment Recript')}}</td>
                <td><a href="{{ \App\Models\Utility::get_file($order->receipt) }}"  title="Invoice" download=""
                    class="btn btn-primary btn-sm action-btn">
                    <i class="ti ti-download"></i>
                </a></td>
            </tr>

        </table>
    </div>
</div>
@if (\Auth::user()->type == 'Owner')
{{ Form::model($order, ['route' => ['bank.status.edit', $order->id], 'method' => 'POST']) }}
<div class="text-end">
    <input type="submit" value="{{ __('Approved') }}" class="btn btn-success rounded" name="status">
    <input type="submit" value="{{ __('Reject') }}" class="btn btn-danger rounded" name="status">
</div>
{{ Form::close() }}
@endif
