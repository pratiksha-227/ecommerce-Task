@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h2 mb-0">Products</h1>
    <div class="d-flex gap-2 align-items-center">
        @auth
            <span class="text-muted small">Logged in as <strong>{{ auth()->user()->name }}</strong> (ID: {{ auth()->id() }})</span>
        @else
            <!-- <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#signinModal">Loginnnnnn</button>
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#signupModal">Sign up</button> -->
        @endauth
        @if(auth()->check() && auth()->user()->is_admin)
            <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
        @endif
    </div>
</div>

<div class="row g-3">
    @forelse($products as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100">
                @if($product->images->isNotEmpty())
                    <img src="{{ asset('storage/' . $product->images->first()->image) }}" class="card-img-top product-img" alt="{{ $product->name }}">
                @else
                    <div class="card-img-top product-img bg-light d-flex align-items-center justify-content-center text-muted">No image</div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text text-muted small">{{ Str::limit($product->description, 80) }}</p>
                    <p class="mb-2"><strong>₹{{ number_format($product->price, 2) }}</strong></p>
                    @if(isset($cartQtyByProduct[$product->id]))
                        <p class="mb-2 small"><span class="badge bg-success">In cart ({{ $cartQtyByProduct[$product->id] }})</span></p>
                    @endif
                    <div class="d-flex gap-2 flex-wrap">
                        <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-sm btn-success">Add to cart</button>
                        </form>
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                        @if(auth()->check() && auth()->user()->is_admin)
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">No products yet. @if(auth()->check() && auth()->user()->is_admin)<a href="{{ route('products.create') }}">Add your first product</a>@else Products will appear here. @endif</div>
        </div>
    @endforelse
</div>
@endsection
