@extends('layouts.admin')

@section('title', 'Blog Details')
@section('page_title', 'Blog Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Post: {{ $blog->title }}</h5>
                <div>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$blog->trashed())
                        <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-sm btn-primary">Edit Post</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4 text-muted">
                    <strong>Category:</strong> 
                    @if($blog->category)
                        <span class="badge bg-light text-dark border">{{ $blog->category->name }}</span>
                    @else
                        <span class="badge bg-light text-dark border">Uncategorized</span>
                    @endif
                    | <strong>Author:</strong> {{ $blog->author ? $blog->author->name : 'N/A' }}
                    | <strong>Status:</strong> 
                    <span class="badge {{ $blog->status === 'published' ? 'bg-success' : 'bg-warning' }}">
                        {{ ucfirst($blog->status) }}
                    </span>
                    | <strong>Slug:</strong> <code>/blog/{{ $blog->slug }}</code>
                </div>

                @if($blog->image)
                    <div class="mb-4 text-center">
                        <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}" class="img-fluid rounded" style="max-height: 400px; object-fit: cover;">
                    </div>
                @endif

                <div class="border p-4 rounded bg-white">
                    {!! $blog->content !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
