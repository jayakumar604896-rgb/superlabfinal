@extends('layouts.admin')

@section('title', 'Gallery Item Details')
@section('page_title', 'Gallery Item Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">{{ $gallery->title ?? 'Gallery Item' }}</h5>
                <div>
                    <a href="{{ route('admin.gallery.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$gallery->trashed())
                        <a href="{{ route('admin.gallery.edit', $gallery->id) }}" class="btn btn-sm btn-primary">Edit Item</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($gallery->image)
                    <div class="text-center mb-4">
                        <img src="{{ asset($gallery->image) }}" alt="{{ $gallery->title ?? 'Gallery item' }}" class="img-fluid rounded border" style="max-height: 360px; object-fit: contain;">
                    </div>
                @endif

                <table class="table table-bordered align-middle">
                    <tr>
                        <th class="w-30 bg-light">ID</th>
                        <td>{{ $gallery->id }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Title</th>
                        <td class="fw-semibold">{{ $gallery->title ?? 'Untitled' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Category</th>
                        <td>{{ $gallery->category?->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Status</th>
                        <td>
                            <span class="badge {{ $gallery->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($gallery->status) }}
                            </span>
                            @if($gallery->trashed())
                                <span class="badge bg-danger ms-1">Trashed</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Created At</th>
                        <td>{{ $gallery->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Updated At</th>
                        <td>{{ $gallery->updated_at->format('M d, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
