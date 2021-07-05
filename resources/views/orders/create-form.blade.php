<form method="post" action="{{ route('orders.process') }}">
    @csrf
    <div class="form-floating mb-3 mb-3">
        @include('partials.input', ['type' => 'text', 'name' => 'customer_name', 'label' => __('Your name')])
    </div>
    <div class="form-floating mb-3 mb-3">
        @include('partials.input', ['type' => 'email', 'name' => 'customer_email', 'label' => __('Your email address')])
    </div>
    <div class="form-floating mb-3 mb-3">
        @include('partials.input', ['type' => 'text', 'name' => 'customer_mobile', 'label' => __('Your mobile number')])
    </div>

    <input type="hidden" name="product_id" value="{{ $product->id }}">

    <button type="submit" class="btn btn-primary position-relative">{{ __('Pay with Placetopay') }}</button>
</form>
