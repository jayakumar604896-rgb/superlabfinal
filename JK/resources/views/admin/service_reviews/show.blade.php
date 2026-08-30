@extends('layouts.admin')

@section('title', 'Review #' . $review->id)
@section('page_title', 'Review Details')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">Review #{{ $review->id }}</h5>
        <a href="{{ route('admin.service-reviews.index') }}" class="btn btn-sm btn-outline-secondary">Back to list</a>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase small fw-bold">Reviewer</h6>
                <p class="mb-0 fs-5 fw-semibold">{{ $review->reviewer_name }}</p>
                @if($review->customer)
                    <p class="text-muted small mb-0">Customer #{{ $review->customer->id }} · {{ $review->customer->mobile }}</p>
                @endif
            </div>
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase small fw-bold">Item reviewed</h6>
                @if($review->service)
                    <p class="mb-0"><span class="badge bg-info text-dark">Test</span> {{ $review->service->title }}</p>
                @elseif($review->package)
                    <p class="mb-0"><span class="badge bg-primary">Package</span> {{ $review->package->name }}</p>
                @else
                    <p class="mb-0 text-muted">Unknown</p>
                @endif
            </div>
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase small fw-bold">Rating</h6>
                <p class="mb-0">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-star {{ $i <= $review->rating ? 'fa-solid text-warning' : 'fa-regular text-muted' }}"></i>
                    @endfor
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase small fw-bold">Status</h6>
                <span class="badge {{ $review->status === 'active' ? 'bg-success' : ($review->status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                    {{ ucfirst($review->status) }}
                </span>
            </div>
            <div class="col-12">
                <h6 class="text-muted text-uppercase small fw-bold">Message</h6>
                <div class="border rounded p-3 bg-light">{{ $review->message }}</div>
            </div>
            <div class="col-12">
                <form method="POST" action="{{ route('admin.service-reviews.update-status', $review->id) }}" class="row g-3 align-items-end">
                    @csrf
                    @method('PUT')
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Update status</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $review->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ $review->status === 'active' ? 'selected' : '' }}>Active (publish on site)</option>
                            <option value="rejected" {{ $review->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Save status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
