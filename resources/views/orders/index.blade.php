@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h2 mb-0">Orders</h1>
    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Products</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>User</th>
                        <th>Date</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->user->name ?? 'N/A' }} <span class="text-muted">(ID: {{ $order->user_id }})</span></td>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td class="text-end">₹{{ number_format($order->subtotal, 2) }}</td>
                            <td class="text-end"><strong>₹{{ number_format($order->total, 2) }}</strong></td>
                            <td>
                                @if($order->status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($order->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($order->status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
        <div class="card-footer">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
