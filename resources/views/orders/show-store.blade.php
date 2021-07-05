@extends('partials.app')

@section('content')
    <div class="row row-cols-1 row-cols-md-4 g-4">
        @foreach($products as $product)
            <div class="col">
                @include('products.card', compact('product'))
            </div>
        @endforeach
    </div>
@endsection
