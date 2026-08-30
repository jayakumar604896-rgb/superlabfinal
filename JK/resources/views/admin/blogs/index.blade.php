@extends('layouts.admin')

@section('title', 'Blogs Management')
@section('page_title', 'Blogs Management')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Blogs (Trash)' : 'Active Blogs' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-newspaper"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.blogs.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Post
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.blogs.index') }}" class="row g-3 mb-4">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search blogs..." value="{{ $search }}">
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
                    <option value="published" {{ $statusFilter == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ $statusFilter == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $blog)
                        <tr>
                            <td>{{ $blog->id }}</td>
                            <td>
                                @if($blog->image)
                                    <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <span class="text-muted small">No Image</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ Str::limit($blog->title, 40) }}</td>
                            <td>
                                @if($blog->category)
                                    <span class="badge bg-light text-dark border">{{ $blog->category->name }}</span>
                                @else
                                    <span class="text-muted small">Uncategorized</span>
                                @endif
                            </td>
                            <td>{{ $blog->author ? $logAuthorName = $blog->author->name : 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $blog->status === 'published' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($blog->status) }}
                                </span>
                            </td>
                            <td>{{ $blog->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                @if($blog->trashed())
                                    <form action="{{ route('admin.blogs.restore', $blog->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                    <form action="{{ route('admin.blogs.force-delete', $blog->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this blog post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-circle-minus"></i> Delete</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.blogs.show', $blog->id) }}" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Soft delete this blog post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No blog posts found.</td>
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
