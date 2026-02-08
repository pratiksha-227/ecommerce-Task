@extends('layouts.app')

@section('title', 'Order Confirmed')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                @if(session('success'))
                    <div class="alert alert-success mb-4">{{ session('success') }}</div>
                @endif
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="fas fa-check text-success fa-2x"></i>
                    </div>
                    <h1 class="h3 mb-1">Order Confirmed</h1>
                    <p class="text-muted mb-0">Order number: <strong>{{ $order->order_number }}</strong></p>
                </div>

                <div class="border rounded p-3 mb-4 bg-light">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="text-muted">Total paid</span>
                        <span class="fs-4 fw-bold">₹{{ number_format($order->total, 2) }}</span>
                    </div>
                    <div class="small text-muted mt-1">Status: {{ ucfirst($order->status) }}</div>
                </div>

                <h2 class="h6 mb-2">Order items</h2>
                <ul class="list-group list-group-flush mb-4">
                    @foreach($order->items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>{{ $item->product->name ?? 'N/A' }} × {{ $item->quantity }}</span>
                            <span>₹{{ number_format($item->total, 2) }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="d-flex gap-2 flex-wrap justify-content-center">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Continue Shopping</a>
                    <a href="{{ route('my-orders.show', $order) }}" class="btn btn-outline-primary">View Order</a>
                    <a href="{{ route('my-orders.index') }}" class="btn btn-outline-secondary">My Orders</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
