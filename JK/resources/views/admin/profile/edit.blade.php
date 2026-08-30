@extends('layouts.admin')

@section('title', 'My Profile')
@section('page_title', 'My Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Profile info -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="m-0 fw-bold"><i class="fa-solid fa-id-card"></i> Account Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block text-secondary">My Role(s)</label>
                        @foreach($user->roles as $role)
                            <span class="badge bg-secondary fs-6 py-2 px-3">{{ $role->name }}</span>
                        @endforeach
                    </div>

                    <div class="border-top pt-3 mt-4 text-end">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-circle-check"></i> Update Info</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="m-0 fw-bold"><i class="fa-solid fa-key text-teal"></i> Change Password</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Include Name and Email hidden to pass validation -->
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password">
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                    </div>

                    <div class="border-top pt-3 mt-4 text-end">
                        <button type="submit" class="btn btn-teal"><i class="fa-solid fa-lock"></i> Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
