@extends('layouts.admin')

@section('title', 'Add Footer Location')
@section('page_title', 'Add Footer Location')

@section('content')
<div class="card shadow-sm col-md-8 mx-auto">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">Create New Footer Location</h5>
        <a href="{{ route('admin.footer-locations.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.footer-locations.store') }}">
            @csrf

            <div class="mb-3">
                <label for="location_name" class="form-label fw-semibold">Location Name <span class="text-danger">*</span></label>
                <input type="text" name="location_name" id="location_name" class="form-control @error('location_name') is-invalid @enderror" value="{{ old('location_name') }}" placeholder="e.g. Lab Test in Delhi" required>
                @error('location_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="map_link" class="form-label fw-semibold">Google Maps Link</label>
                <input type="url" name="map_link" id="map_link" class="form-control @error('map_link') is-invalid @enderror" value="{{ old('map_link') }}" placeholder="https://maps.google.com/?q=Location">
                <div class="form-text">Optional link to Google Maps for this location.</div>
                @error('map_link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.footer-locations.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Save Location</button>
            </div>
        </form>
    </div>
</div>
@endsection
