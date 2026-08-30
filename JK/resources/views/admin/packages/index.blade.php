@extends('layouts.admin')

@section('title', 'Package Management')
@section('page_title', 'Health Check Packages')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Packages (Trash)' : 'Packages List' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.packages.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-box-archive"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.packages.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
                <a href="{{ route('admin.packages.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Package
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.packages.index') }}" class="row g-3 mb-4">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search packages..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter by Status</option>
                    <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $statusFilter == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 10%;">Badge</th>
                        <th style="width: 25%;">Package Name</th>
                        <th style="width: 12%;">Original Price</th>
                        <th style="width: 12%;">Offer Price</th>
                        <th style="width: 10%;">Discount</th>
                        <th style="width: 10%;">Tests Count</th>
                        <th style="width: 8%;">Status</th>
                        <th class="text-end" style="width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                @if($item->badge)
                                    <span class="badge bg-danger text-uppercase" style="font-size: 0.75rem;">{{ $item->badge }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="fw-semibold text-dark">{{ $item->name }}</td>
                            <td class="text-decoration-line-through text-muted">₹{{ number_format($item->original_price) }}</td>
                            <td class="fw-bold text-teal text-success">₹{{ number_format($item->offer_price) }}</td>
                            <td>
                                @if($item->discount_percentage)
                                    <span class="badge bg-warning text-dark">{{ $item->discount_percentage }}% OFF</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $item->tests_included_count }} tests</span></td>
                            <td>
                                <span class="badge {{ $item->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($item->trashed())
                                    <form action="{{ route('admin.packages.restore', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                    <form action="{{ route('admin.packages.force-delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this package?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-circle-minus"></i> Delete</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.packages.show', $item->id) }}" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('admin.packages.edit', $item->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('admin.packages.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Soft delete this package?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No packages found.</td>
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
