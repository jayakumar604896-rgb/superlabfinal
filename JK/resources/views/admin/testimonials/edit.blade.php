@extends('layouts.admin')

@section('title', 'Edit Testimonial')
@section('page_title', 'Edit Testimonial')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Edit Testimonial Details</h5>
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.testimonials.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="client_name" class="form-label">Client Name</label>
                            <input type="text" class="form-control @error('client_name') is-invalid @enderror" id="client_name" name="client_name" value="{{ old('client_name', $testimonial->client_name) }}">
                            @error('client_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="client_designation" class="form-label">Designation / Role</label>
                            <input type="text" class="form-control @error('client_designation') is-invalid @enderror" id="client_designation" name="client_designation" value="{{ old('client_designation', $testimonial->client_designation) }}">
                            @error('client_designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-select @error('rating') is-invalid @enderror" id="rating" name="rating">
                                <option value="5" {{ old('rating', $testimonial->rating) == '5' ? 'selected' : '' }}>5 Stars</option>
                                <option value="4" {{ old('rating', $testimonial->rating) == '4' ? 'selected' : '' }}>4 Stars</option>
                                <option value="3" {{ old('rating', $testimonial->rating) == '3' ? 'selected' : '' }}>3 Stars</option>
                                <option value="2" {{ old('rating', $testimonial->rating) == '2' ? 'selected' : '' }}>2 Stars</option>
                                <option value="1" {{ old('rating', $testimonial->rating) == '1' ? 'selected' : '' }}>1 Star</option>
                            </select>
                            @error('rating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="client_image" class="form-label">Client Photo</label>
                            @if($testimonial->client_image)
                                <div class="mb-2">
                                    <img src="{{ asset($testimonial->client_image) }}" alt="{{ $testimonial->client_name }}" class="rounded-circle img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('client_image') is-invalid @enderror" id="client_image" name="client_image" accept="image/*">
                            @error('client_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Testimonial Message</label>
                        <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="4">{{ old('message', $testimonial->message) }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="active" {{ old('status', $testimonial->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $testimonial->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update Testimonial</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
