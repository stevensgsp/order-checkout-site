@extends('partials.app')

@section('content')
    <div class="row">
        <div class="col-9">
            <div class="card p-2">
                <div class="m-3">
                    <h3>{{ __('Enter the requested data to buy') }}</h3>

                    @include('orders.create-form')
                </div>
            </div>
        </div>

        <div class="col-3">
            <div class="card">
                <div class="m-3">
                    <h3>{{ __('You are buying') }}:</h3>

                    @include('products.card', ['showBuyButton' => false])

                    <div class="row mt-3">
                        <div class="col">
                            <span>Subtotal</span>
                        </div>
                        <div class="col text-end">
                            {{ $product->currency }} {{ $product->real_price }}
                        </div>
                    </div>

                    <div class="row fw-bold">
                        <div class="col">
                            <span>TOTAL TO PAY:</span>
                        </div>
                        <div class="col text-end">
                            {{ $product->currency }} {{ $product->real_price }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection