@extends('partials.app')

@section('content')
    <div class="row">
        <div class="col">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Product') }}</th>
                        <th scope="col">{{ __('Price') }}</th>
                        <th scope="col">{{ __('Client') }}</th>
                        <th scope="col">{{ __('Payment status') }}</th>
                        <th scope="col">{{ __('Order status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <th scope="row">{{ $order->id }}</th>
                        <td width="300">
                            @include('products.card', ['product' => $order->product, 'showBuyButton' => false])
                        </td>
                        <td>{{ $order->currency }} {{ $order->real_price }}</td>
                        <td>
                            <span>{{ $order->customer_name }}</span><br>
                            <span>{{ $order->customer_email }}</span><br>
                            <span>{{ $order->customer_mobile }}</span>
                        </td>
                        <td>
                            @if(! empty($order->payment_status))
                                <span class="badge bg-{{ $order->payment_status_class }}">
                                    {{ $order->payment_status }}
                                </span>
                                <br>
                                <span>{{ $order->payment_status_message }}</span>
                                <br>
                            @endif

                            @if($order->payment_status !== 'APPROVED')
                                <a
                                    href="{{ route('orders.reprocess', ['orderId' => $order->id]) }}"
                                    class="btn btn-primary btn-sm"
                                >{{ __('Pay') }}</a>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->status_class }}">{{ $order->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection