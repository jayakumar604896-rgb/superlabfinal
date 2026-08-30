@extends('layouts.admin')

@section('title', 'Test Details')
@section('page_title', 'Test Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Test: {{ $service->title }}</h5>
                <div>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$service->trashed())
                        <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-sm btn-primary">Edit Test</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <tr>
                        <th class="w-25 bg-light">ID</th>
                        <td>{{ $service->id }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Image</th>
                        <td>
                            @if($service->image)
                                <img src="{{ asset($service->image) }}" alt="{{ $service->title }}" class="img-thumbnail" style="width: 150px; max-height: 150px; object-fit: cover;">
                            @else
                                <span class="text-muted">No image uploaded.</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Test Title</th>
                        <td class="fw-semibold">{{ $service->title }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Category</th>
                        <td>
                            @if($service->category)
                                <span class="badge bg-light text-dark border">{{ $service->category->name }}</span>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Slug</th>
                        <td><code>{{ $service->slug }}</code></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Price</th>
                        <td class="fw-bold text-primary">₹{{ number_format($service->price) }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Short Description</th>
                        <td>{{ $service->short_description ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Full Description</th>
                        <td>{!! nl2br(e($service->description)) !!}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Status</th>
                        <td>
                            <span class="badge {{ $service->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($service->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Created At</th>
                        <td>{{ $service->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
