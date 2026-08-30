@extends('layouts.admin')

@section('title', 'Payment Management')
@section('page_title', 'Payment History')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Payments (Trash)' : 'Payment Records List' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-box-archive"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.payments.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
                <a href="{{ route('admin.payments.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus"></i> Record Payment
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3 mb-4">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search Txn ID, BK#, Customer Name..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter by Status</option>
                    <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="success" {{ $statusFilter == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed" {{ $statusFilter == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ $statusFilter == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%;">Txn ID</th>
                        <th style="width: 15%;">Booking Reference</th>
                        <th style="width: 20%;">Customer</th>
                        <th style="width: 15%;">Payment Type</th>
                        <th style="width: 12%;">Amount</th>
                        <th style="width: 10%;">Status</th>
                        <th class="text-end" style="width: 13%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $item)
                        <tr>
                            <td class="fw-bold">{{ $item->transaction_id ?? 'Txn-' . $item->id }}</td>
                            <td>
                                @if($item->booking)
                                    <a href="{{ route('admin.bookings.show', $item->booking->id) }}" class="fw-semibold">
                                        {{ $item->booking->booking_number }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($item->booking)
                                    <div class="fw-semibold text-dark">{{ $item->booking->customer_name }}</div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $item->paymentType ? $item->paymentType->name : 'N/A' }}</td>
                            <td class="fw-bold text-dark">₹{{ number_format($item->amount) }}</td>
                            <td>
                                <span class="badge {{ $item->status === 'success' ? 'bg-success' : ($item->status === 'failed' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($item->trashed())
                                    <form action="{{ route('admin.payments.restore', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                    <form action="{{ route('admin.payments.force-delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this payment record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-circle-minus"></i> Delete</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.payments.show', $item->id) }}" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('admin.payments.edit', $item->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('admin.payments.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Soft delete this payment record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No payment records found.</td>
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
