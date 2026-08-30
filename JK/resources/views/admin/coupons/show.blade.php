@extends('layouts.admin')

@section('title', 'Coupon Details')
@section('page_title', 'Coupon Details')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="m-0 fw-bold text-primary">{{ $coupon->code }}</h5>
                    <p class="text-muted small mb-0">{{ $coupon->name ?: 'No display name' }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-pen me-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0 align-middle">
                    <tr>
                        <th class="bg-light w-35">Status</th>
                        <td>
                            <span class="badge rounded-pill {{ $coupon->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($coupon->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Discount</th>
                        <td>
                            @if($coupon->discount_type === 'percent')
                                <span class="fw-semibold">{{ rtrim(rtrim(number_format($coupon->discount_value, 2), '0'), '.') }}% off</span>
                                @if($coupon->max_discount_amount)
                                    <span class="text-muted">(max ₹{{ number_format($coupon->max_discount_amount) }})</span>
                                @endif
                            @else
                                <span class="fw-semibold">₹{{ number_format($coupon->discount_value) }} off</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Minimum order</th>
                        <td>{{ $coupon->min_order_amount ? '₹' . number_format($coupon->min_order_amount) : 'None' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Usage</th>
                        <td>
                            {{ $coupon->usage_count }} used
                            @if($coupon->usage_limit)
                                of {{ $coupon->usage_limit }} allowed
                            @else
                                · unlimited
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Valid from</th>
                        <td>{{ $coupon->starts_at ? $coupon->starts_at->format('d M Y, h:i A') : 'Immediately' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Valid until</th>
                        <td>{{ $coupon->expires_at ? $coupon->expires_at->format('d M Y, h:i A') : 'No expiry' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Guest checkout</th>
                        <td>
                            @if($coupon->guest_eligible)
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">Allowed</span>
                            @else
                                <span class="text-muted">Not allowed</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold">Assigned customers</h6>
            </div>
            <div class="card-body">
                @forelse($coupon->customers as $customer)
                    <div class="d-flex align-items-center justify-content-between py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <div class="fw-semibold">{{ $customer->name }}</div>
                            <div class="text-muted small">{{ $customer->mobile }}@if($customer->email) · {{ $customer->email }}@endif</div>
                        </div>
                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                    </div>
                @empty
                    <p class="text-muted mb-0">
                        No specific customers assigned.
                        @if($coupon->guest_eligible)
                            Guest eligibility applies to everyone at checkout.
                        @else
                            This coupon cannot be used until customers are assigned or guest checkout is enabled.
                        @endif
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold">At checkout</h6>
            </div>
            <div class="card-body">
                <div class="text-center p-3 rounded border border-dashed bg-light mb-3">
                    <div class="display-6 fw-bold text-primary">{{ $coupon->code }}</div>
                    <div class="text-muted small mt-1">
                        @if($coupon->discount_type === 'percent')
                            {{ rtrim(rtrim(number_format($coupon->discount_value, 2), '0'), '.') }}% discount
                        @else
                            ₹{{ number_format($coupon->discount_value) }} off
                        @endif
                    </div>
                </div>
                <ul class="list-unstyled small mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Guests</span>
                        <span>{{ $coupon->guest_eligible ? 'Yes' : 'No' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Assigned accounts</span>
                        <span>{{ $coupon->customers->count() }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
