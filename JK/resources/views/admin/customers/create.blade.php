@extends('layouts.admin')

@section('title', 'Add New Customer')
@section('page_title', 'Add New Customer')

@section('content')
<div class="card shadow-sm col-md-8 mx-auto">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">Add New Lab Customer</h5>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.customers.store') }}">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Full Name" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" placeholder="10-digit Mobile Number" required>
                    @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="email@example.com">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min 6 characters" required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Age</label>
                    <input type="number" name="age" class="form-control" value="{{ old('age') }}" placeholder="Age in years">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Blood Group</label>
                    <select name="blood_group" class="form-select">
                        <option value="Unknown" {{ old('blood_group') == 'Unknown' ? 'selected' : '' }}>Unknown</option>
                        <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Residential Address</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Full residential address">{{ old('address') }}</textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Emergency Contact Person</label>
                    <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}" placeholder="e.g. Radhika (Spouse)">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}" placeholder="e.g. +91 9876543211">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Save Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
