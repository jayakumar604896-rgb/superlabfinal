@extends('layouts.admin')

@section('title', 'Location Management')
@section('page_title', 'Locations')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Locations (Trash)' : 'Locations List' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.locations.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-location-dot"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.locations.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
                <a href="{{ route('admin.locations.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Location
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.locations.index') }}" class="row g-3 mb-4">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search locations..." value="{{ $search }}">
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
                        <th style="width: 8%;">ID</th>
                        <th style="width: 25%;">Name</th>
                        <th style="width: 30%;">Address</th>
                        <th style="width: 15%;">Phone</th>
                        <th style="width: 10%;">Status</th>
                        <th class="text-end" style="width: 12%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td class="fw-semibold text-dark">{{ $item->name }}</td>
                            <td>{{ Str::limit($item->address, 60) }}</td>
                            <td>{{ $item->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $item->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($item->trashed())
                                    <form action="{{ route('admin.locations.restore', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                    <form action="{{ route('admin.locations.force-delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this location?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-circle-minus"></i> Delete</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.locations.show', $item->id) }}" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('admin.locations.edit', $item->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('admin.locations.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Soft delete this location?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No locations found.</td>
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
