@extends('layouts.admin')

@section('title', 'Category Details')
@section('page_title', 'Category Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Category: {{ $category->name }}</h5>
                <div>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$category->trashed())
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-primary">Edit Category</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <tr>
                        <th class="w-25 bg-light">ID</th>
                        <td>{{ $category->id }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Image</th>
                        <td>
                            @if($category->image)
                                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="img-thumbnail" style="width: 150px; max-height: 150px; object-fit: cover;">
                            @else
                                <span class="text-muted">No image uploaded.</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Category Name</th>
                        <td class="fw-semibold">{{ $category->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Slug</th>
                        <td><code>{{ $category->slug }}</code></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Description</th>
                        <td>{{ $category->description ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Status</th>
                        <td>
                            <span class="badge {{ $category->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($category->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Created At</th>
                        <td>{{ $category->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
