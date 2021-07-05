@php
    $showBuyButton = $showBuyButton ?? true;
@endphp

<div class="card h-100">
    <img src="{{ $product->image_url }}" class="card-img-top" alt="Image">
    <div class="card-body">
        <h5 class="card-title">{{ $product->name }}</h5>
        <p class="card-text">{{ $product->description }}</p>

        @if($showBuyButton)
            <a
                href="{{ route('orders.create', ['productId' => $product->id]) }}"
                class="btn btn-primary position-relative"
            >
                {{ __('Buy') }}
                <span
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                >{{ $product->currency }} {{ $product->real_price }}</span>
            </a>
        @endif
    </div>
</div>
