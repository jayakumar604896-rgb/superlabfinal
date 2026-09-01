@extends('layouts.admin')

@section('title', 'Footer Location Management')
@section('page_title', 'Footer Locations')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Footer Locations (Trash)' : 'Footer Locations List' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.footer-locations.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-map-location-dot"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.footer-locations.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
                <a href="{{ route('admin.footer-locations.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Footer Location
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.footer-locations.index') }}" class="row g-3 mb-4">
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
                        <th style="width: 30%;">Location Name</th>
                        <th style="width: 35%;">Google Maps Link</th>
                        <th style="width: 12%;">Status</th>
                        <th class="text-end" style="width: 15%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td class="fw-semibold text-dark">{{ $item->location_name }}</td>
                            <td>
                                @if($item->map_link)
                                    <a href="{{ $item->map_link }}" target="_blank" class="text-decoration-none text-primary">
                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> {{ Str::limit($item->map_link, 50) }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $item->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($item->trashed())
                                    <form action="{{ route('admin.footer-locations.restore', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1" title="Restore"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                    <form action="{{ route('admin.footer-locations.force-delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this location?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Permanently"><i class="fa-solid fa-circle-minus"></i> Delete</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.footer-locations.show', $item->id) }}" class="btn btn-sm btn-outline-info me-1" title="View"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('admin.footer-locations.edit', $item->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('admin.footer-locations.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Soft delete this footer location?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Soft Delete"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No footer locations found.</td>
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
