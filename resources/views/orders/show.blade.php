@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h2 mb-0">Order {{ $order->order_number }}</h1>
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Back to Orders</a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="h5 mb-0">Order Items</h2>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                                <td class="text-end">₹{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h2 class="h5 mb-0">Summary</h2>
            </div>
            <div class="card-body">
                <p class="d-flex justify-content-between mb-2">
                    <span class="text-muted">User</span>
                    <span>{{ $order->user->name ?? 'N/A' }} (ID: {{ $order->user_id }})</span>
                </p>
                <p class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Date</span>
                    <span>{{ $order->created_at->format('M d, Y H:i') }}</span>
                </p>
                <p class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Status</span>
                    @if($order->status === 'paid')
                        <span class="badge bg-success">Paid</span>
                    @elseif($order->status === 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                    @endif
                </p>
                <hr>
                <p class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>₹{{ number_format($order->subtotal, 2) }}</span>
                </p>
                @if((float) $order->tax > 0)
                <p class="d-flex justify-content-between mb-2">
                    <span>Tax</span>
                    <span>₹{{ number_format($order->tax, 2) }}</span>
                </p>
                @endif
                <p class="d-flex justify-content-between mb-0 fs-5">
                    <strong>Total</strong>
                    <strong>₹{{ number_format($order->total, 2) }}</strong>
                </p>
                @if($order->payment_id)
                    <hr>
                    <p class="small text-muted mb-0">Payment ID: {{ $order->payment_id }}</p>
                @endif
                @if($order->notes)
                    <p class="small text-muted mt-2 mb-0">Notes: {{ $order->notes }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
