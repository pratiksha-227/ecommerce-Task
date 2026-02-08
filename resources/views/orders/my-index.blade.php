@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h2 mb-0">My Orders</h1>
    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Continue Shopping</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td class="text-end"><strong>₹{{ number_format($order->total, 2) }}</strong></td>
                            <td>
                                @if($order->status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($order->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('my-orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">You have no orders yet. <a href="{{ route('products.index') }}">Start shopping</a></td>
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
