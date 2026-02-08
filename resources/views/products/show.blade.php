@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="row">
    <div class="col-lg-6">
        @if($product->images->isNotEmpty())
            <div class="rounded overflow-hidden bg-light" style="min-height: 300px;">
                <img id="productMainImg" src="{{ asset('storage/' . $product->images->first()->image) }}" class="d-block w-100 rounded" style="max-height: 400px; object-fit: contain; background: #f0f0f0; cursor: pointer;" alt="{{ $product->name }}">
            </div>
            <p class="small text-muted mt-1 mb-0">Select the image you want for the cart:</p>
            <div class="d-flex gap-2 mt-2 flex-wrap" id="productThumbs">
                @foreach($product->images as $index => $img)
                    <img src="{{ asset('storage/' . $img->image) }}" class="img-thumb product-thumb {{ $index === 0 ? 'border border-primary' : '' }}" alt="" data-src="{{ asset('storage/' . $img->image) }}" data-image-id="{{ $img->id }}" role="button" style="cursor: pointer;">
                @endforeach
            </div>
        @else
            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="height: 300px;">No images</div>
        @endif
    </div>
    <div class="col-lg-6">
        <h1 class="h2">{{ $product->name }}</h1>
        <p class="text-muted">₹{{ number_format($product->price, 2) }}</p>
        @if($product->description)
            <p class="lead">{{ $product->description }}</p>
        @endif
        <p class="text-muted small">Quantity in stock: {{ $product->quantity }}</p>
        <form action="{{ route('cart.add') }}" method="POST" class="d-inline-flex align-items-center gap-2 mt-3" id="addToCartForm">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="product_image_id" id="selectedImageId" value="{{ $product->images->first()?->id }}">
            <label for="qty" class="form-label mb-0 small">Qty:</label>
            <input type="number" name="quantity" id="qty" value="1" min="1" class="form-control form-control-sm" style="width: 70px;">
            <button type="submit" class="btn btn-success">Add to cart</button>
        </form>
        <div class="d-flex gap-2 mt-3">
            @if(auth()->check() && auth()->user()->is_admin)
                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">Edit Product</a>
            @endif
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Back to list</a>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var mainImg = document.getElementById('productMainImg');
    var thumbs = document.querySelectorAll('.product-thumb');
    if (mainImg && thumbs.length) {
        var selectedInput = document.getElementById('selectedImageId');
        thumbs.forEach(function(thumb) {
            thumb.addEventListener('click', function() {
                var src = this.getAttribute('data-src');
                var imageId = this.getAttribute('data-image-id');
                if (src) {
                    mainImg.src = src;
                    if (selectedInput && imageId) selectedInput.value = imageId;
                    thumbs.forEach(function(t) { t.classList.remove('border', 'border-primary'); });
                    this.classList.add('border', 'border-primary');
                }
            });
        });
    }
});
</script>
@endpush
@endsection
