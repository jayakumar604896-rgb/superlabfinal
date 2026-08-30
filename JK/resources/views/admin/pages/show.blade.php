@extends('layouts.admin')

@section('title', 'Page Details')
@section('page_title', 'Page Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Page: {{ $page->title }}</h5>
                <div>
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$page->trashed())
                        <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-sm btn-primary">Edit Page</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <strong>Slug:</strong> <code>/{{ $page->slug }}</code> | 
                    <strong>Status:</strong> 
                    <span class="badge {{ $page->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($page->status) }}
                    </span>
                </div>

                <div class="border p-4 rounded bg-white mb-4">
                    <h5 class="fw-bold border-bottom pb-2 mb-3 text-secondary">Page Content Preview</h5>
                    {!! $page->content !!}
                </div>

                <div class="card bg-light border-0">
                    <div class="card-body py-3">
                        <h6 class="fw-bold text-secondary mb-2"><i class="fa-solid fa-search"></i> SEO Information</h6>
                        <p class="mb-1"><strong>Meta Title:</strong> {{ $page->meta_title ?? 'None' }}</p>
                        <p class="mb-0"><strong>Meta Description:</strong> {{ $page->meta_description ?? 'None' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
