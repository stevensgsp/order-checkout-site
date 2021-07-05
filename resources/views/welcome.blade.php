@extends('partials.app')

@section('content')
    <span>Welcome to Order Checkout Site! A basic store, where a customer can only buy a single product with a fixed value.</span>
    <br>
    <a href="{{ route('orders.show-store') }}">Go to store</a>
@endsection
