@extends('layouts.admin')

@section('title', 'Categories Management')
@section('page_title', 'Categories Management')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Categories (Trash)' : 'Active Categories' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-tags"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.categories.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Category
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-3 mb-4">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ $search }}">
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
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $cat)
                        <tr>
                            <td>{{ $cat->id }}</td>
                            <td>
                                @if($cat->image)
                                    <img src="{{ asset($cat->image) }}" alt="{{ $cat->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <span class="text-muted small">No Image</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $cat->name }}</td>
                            <td><code>{{ $cat->slug }}</code></td>
                            <td>
                                <span class="badge {{ $cat->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($cat->status) }}
                                </span>
                            </td>
                            <td>{{ $cat->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                @if($cat->trashed())
                                    <form action="{{ route('admin.categories.restore', $cat->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                    <form action="{{ route('admin.categories.force-delete', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-circle-minus"></i> Delete</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.categories.show', $cat->id) }}" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Soft delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No categories found.</td>
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
