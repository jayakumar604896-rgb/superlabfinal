@extends('layouts.admin')

@section('title', 'Coupon Management')
@section('page_title', 'Coupons')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div>
            <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Coupons' : 'Coupons' }}</h5>
            <p class="text-muted small mb-0 mt-1">Manage discount codes for guest and registered customers.</p>
        </div>
        <div class="d-flex gap-2">
            @if($withTrashed)
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Active coupons
                </a>
            @else
                <a href="{{ route('admin.coupons.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger">
                    <i class="fa-solid fa-trash-can me-1"></i> Archive
                </a>
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> New coupon
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="row g-2 mb-4 align-items-end">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-5">
                <label class="form-label small text-muted mb-1">Search</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Code or name…" value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="active" @selected($statusFilter === 'active')>Active</option>
                    <option value="inactive" @selected($statusFilter === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary w-100" type="submit">Apply</button>
            </div>
            @if($search || $statusFilter)
            <div class="col-md-2">
                <a href="{{ route('admin.coupons.index', $withTrashed ? ['trashed' => 'true'] : []) }}" class="btn btn-link text-decoration-none w-100">Clear</a>
            </div>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Coupon</th>
                        <th>Discount</th>
                        <th>Audience</th>
                        <th>Usage</th>
                        <th>Validity</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $coupon)
                        <tr>
                            <td>
                                <div class="fw-bold text-primary">{{ $coupon->code }}</div>
                                <div class="text-muted small">{{ $coupon->name ?: '—' }}</div>
                            </td>
                            <td>
                                @if($coupon->discount_type === 'percent')
                                    <span class="fw-semibold">{{ rtrim(rtrim(number_format($coupon->discount_value, 2), '0'), '.') }}%</span>
                                    @if($coupon->max_discount_amount)
                                        <div class="text-muted small">Max ₹{{ number_format($coupon->max_discount_amount) }}</div>
                                    @endif
                                @else
                                    <span class="fw-semibold">₹{{ number_format($coupon->discount_value) }}</span>
                                @endif
                                @if($coupon->min_order_amount)
                                    <div class="text-muted small">Min ₹{{ number_format($coupon->min_order_amount) }}</div>
                                @endif
                            </td>
                            <td>
                                @if($coupon->guest_eligible)
                                    <span class="badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle">Guests</span>
                                @endif
                                @if($coupon->customers_count > 0)
                                    <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis border border-primary-subtle">{{ $coupon->customers_count }} assigned</span>
                                @endif
                                @if(!$coupon->guest_eligible && $coupon->customers_count === 0)
                                    <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">Needs setup</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $coupon->usage_count }}</span>
                                @if($coupon->usage_limit)
                                    <span class="text-muted">/ {{ $coupon->usage_limit }}</span>
                                @else
                                    <span class="text-muted small d-block">Unlimited</span>
                                @endif
                            </td>
                            <td class="small">
                                @if($coupon->starts_at || $coupon->expires_at)
                                    @if($coupon->starts_at)
                                        <div><span class="text-muted">From</span> {{ $coupon->starts_at->format('d M Y') }}</div>
                                    @endif
                                    @if($coupon->expires_at)
                                        <div><span class="text-muted">Until</span> {{ $coupon->expires_at->format('d M Y') }}</div>
                                    @endif
                                @else
                                    <span class="text-muted">No expiry</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $coupon->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($coupon->status) }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                @if($coupon->trashed())
                                    <form action="{{ route('admin.coupons.restore', $coupon->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">Restore</button></form>
                                    <form action="{{ route('admin.coupons.force-delete', $coupon->id) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
                                @else
                                    <a href="{{ route('admin.coupons.show', $coupon->id) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="fa-regular fa-eye"></i></a>
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Archive"><i class="fa-solid fa-box-archive"></i></button></form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="fa-solid fa-ticket fa-2x opacity-50"></i></div>
                                <div class="fw-semibold">No coupons found</div>
                                <p class="text-muted small mb-3">Create your first discount code to use at checkout.</p>
                                @if(!$withTrashed)
                                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-sm btn-primary">Create coupon</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="mt-4">{{ $records->links() }}</div>
        @endif
    </div>
</div>
@endsection
