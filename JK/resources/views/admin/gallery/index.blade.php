@extends('layouts.admin')

@section('title', 'Gallery Management')
@section('page_title', 'Gallery Management')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Gallery (Trash)' : 'Gallery Items' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.gallery.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-images"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.gallery.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
                <a href="{{ route('admin.gallery.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Upload Item
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.gallery.index') }}" class="row g-3 mb-4">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by title..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter by Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryFilter == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter by Status</option>
                    <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $statusFilter == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </form>

        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            @forelse($records as $item)
                <div class="col">
                    <div class="card h-100 border shadow-sm">
                        <img src="{{ asset($item->image) }}" class="card-img-top" alt="{{ $item->title ?? 'Gallery item' }}" style="height: 180px; object-fit: cover;">
                        <div class="card-body py-3">
                            <h6 class="card-title fw-bold text-dark mb-1">{{ $item->title ?? 'Untitled Item' }}</h6>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="badge {{ $item->status === 'active' ? 'bg-success' : 'bg-secondary' }}" style="font-size: 0.75rem;">
                                    {{ ucfirst($item->status) }}
                                </span>
                                @if($item->category)
                                    <small class="text-secondary">{{ $item->category->name }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 text-end">
                            @if($item->trashed())
                                <form action="{{ route('admin.gallery.restore', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success py-1 px-2"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                </form>
                                <form action="{{ route('admin.gallery.force-delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this gallery item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger py-1 px-2"><i class="fa-solid fa-circle-minus"></i> Delete</button>
                                </form>
                            @else
                                <a href="{{ route('admin.gallery.show', $item->id) }}" class="btn btn-sm btn-outline-info py-1 px-2 me-1" title="View"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('admin.gallery.edit', $item->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2 me-1"><i class="fa-solid fa-pen"></i> Edit</a>
                                <form action="{{ route('admin.gallery.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Soft delete this item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2"><i class="fa-solid fa-trash-can"></i></button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 w-100 text-center py-5">
                    <span class="text-muted">No gallery items found.</span>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
