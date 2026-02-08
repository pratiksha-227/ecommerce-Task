@extends('layouts.app')

@section('title', 'Cart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h2 mb-0">Your Cart</h1>
    <div class="d-flex align-items-center gap-3">
        <p class="mb-0 text-muted small">User ID: <strong>{{ $userId }}</strong></p>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">Continue Shopping</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-bordered bg-white">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>User ID</th>
                <th>Product</th>
                <th>Images</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->user_id }}</td>
                    <td>
                        <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                        <br><small class="text-muted">ID: {{ $item->product_id }}</small>
                    </td>
                    <td>
                        @if($item->selectedImage)
                            <img src="{{ asset('storage/' . $item->selectedImage->image) }}" class="img-thumb" alt="">
                        @elseif($item->product && $item->product->images->isNotEmpty())
                            <div class="d-flex gap-1 flex-wrap">
                                @foreach($item->product->images->take(3) as $img)
                                    <img src="{{ asset('storage/' . $img->image) }}" class="img-thumb" alt="">
                                @endforeach
                                @if($item->product->images->count() > 3)
                                    <span class="align-middle text-muted">+{{ $item->product->images->count() - 3 }}</span>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>₹{{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td><strong>₹{{ number_format($item->total, 2) }}</strong></td>
                    <td>
                        <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this item from cart?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove from cart">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No items in cart. <a href="{{ route('products.index') }}">Add products</a></td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($items->isNotEmpty())
    @php $cartTotal = $items->sum('total'); @endphp
    <div class="card mt-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="text-muted">Cart total:</span>
                <span class="fs-4 fw-bold ms-2">₹{{ number_format($cartTotal, 2) }}</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Continue Shopping</a>
                <form action="{{ route('checkout.store') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg">Proceed to Checkout</button>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
