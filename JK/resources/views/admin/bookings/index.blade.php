@extends('layouts.admin')

@section('title', 'Booking Management')
@section('page_title', 'Booking History')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Bookings (Trash)' : 'Bookings List' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-box-archive"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.bookings.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
                <a href="{{ route('admin.bookings.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus"></i> Add New Booking
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-3 mb-4">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by BK#, Name, Email, Phone..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter by Booking Status</option>
                    <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $statusFilter == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="payment_status" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter by Payment Status</option>
                    <option value="pending" {{ $paymentStatusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $paymentStatusFilter == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ $paymentStatusFilter == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ $paymentStatusFilter == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">Booking #</th>
                        <th style="width: 18%;">Customer</th>
                        <th style="width: 16%;">Tests / Items</th>
                        <th style="width: 12%;">Package</th>
                        <th style="width: 10%;">Booking Date</th>
                        <th style="width: 10%;">Price</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 10%;">Payment</th>
                        <th class="text-end" style="width: 8%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $item)
                        <tr>
                            <td><span class="fw-bold">{{ $item->booking_number }}</span></td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $item->customer_name }}</div>
                                <small class="text-muted">{{ $item->customer_phone ?: $item->customer_email }}</small>
                                @if($item->customer)
                                    <br><a href="{{ route('admin.customers.show', $item->customer->id) }}" class="small text-decoration-none">
                                        <i class="fa-solid fa-link"></i> Linked account
                                    </a>
                                @endif
                            </td>
                            <td>
                                @php $lineItems = $item->lineItemsForDisplay(); @endphp
                                @if(count($lineItems))
                                    <span class="text-dark">{{ $lineItems[0]['name'] ?? 'Item' }}</span>
                                    @if(count($lineItems) > 1)
                                        <small class="text-muted d-block">+{{ count($lineItems) - 1 }} more</small>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($item->package)
                                    <span class="text-dark">{{ $item->package->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $item->booking_date ? $item->booking_date->format('M d, Y') : 'N/A' }}</td>
                            <td class="fw-bold text-dark">₹{{ number_format($item->total_price) }}</td>
                            <td>
                                @php
                                    $statusBadge = 'bg-secondary';
                                    if ($item->status === 'completed') $statusBadge = 'bg-success';
                                    elseif ($item->status === 'cancelled') $statusBadge = 'bg-danger';
                                @endphp
                                <span class="badge {{ $statusBadge }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $payBadge = 'bg-secondary';
                                    if ($item->payment_status === 'paid') $payBadge = 'bg-success';
                                    elseif ($item->payment_status === 'failed') $payBadge = 'bg-danger';
                                    elseif ($item->payment_status === 'refunded') $payBadge = 'bg-warning text-dark';
                                @endphp
                                <span class="badge {{ $payBadge }}">
                                    {{ ucfirst($item->payment_status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($item->trashed())
                                    <form action="{{ route('admin.bookings.restore', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                    <form action="{{ route('admin.bookings.force-delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this booking?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-circle-minus"></i> Delete</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.bookings.show', $item->id) }}" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('admin.bookings.edit', $item->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('admin.bookings.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Soft delete this booking?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
