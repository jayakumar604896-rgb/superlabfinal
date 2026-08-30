@extends('layouts.admin')

@section('title', 'Customers Management')
@section('page_title', 'Customers Management')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Customers (Trash)' : 'Active Lab Customers' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-users"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.customers.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
                <a href="{{ route('admin.customers.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-user-plus"></i> Add Customer
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <!-- Search and Filters Form -->
        <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3 mb-4">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-5">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, phone, or email..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter by Status</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            @if($search || $status)
                <div class="col-md-2">
                    <a href="{{ route('admin.customers.index', ['trashed' => $withTrashed ? 'true' : 'false']) }}" class="btn btn-outline-danger w-100">Clear Filters</a>
                </div>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Age / Gender</th>
                        <th>Blood Group</th>
                        <th>Status</th>
                        <th>Registered Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td class="fw-bold text-primary">{{ $customer->name }}</td>
                            <td><i class="fa-solid fa-phone me-1 text-muted"></i>{{ $customer->mobile }}</td>
                            <td>{{ $customer->email ?: 'N/A' }}</td>
                            <td>
                                @if($customer->age || $customer->gender)
                                    <span class="badge bg-light text-dark border">{{ $customer->age ? $customer->age . ' yrs' : '' }} {{ $customer->gender }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->blood_group && $customer->blood_group !== 'Unknown')
                                    <span class="badge bg-danger">{{ $customer->blood_group }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $customer->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                            <td>{{ $customer->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                @if($customer->trashed())
                                    <form action="{{ route('admin.customers.restore', $customer->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1" title="Restore"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                    <form action="{{ route('admin.customers.force-delete', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this customer?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Permanently Delete"><i class="fa-solid fa-circle-xmark"></i> Delete</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-info me-1" title="View Customer Profile">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Customer">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Customer"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-user-slash fs-3 d-block mb-2"></i>
                                No customer records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
