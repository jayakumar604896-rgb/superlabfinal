@extends('layouts.admin')

@section('title', 'Edit User')
@section('page_title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Edit User Information</h5>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
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
                        <label for="password" class="form-label">Password <span class="text-muted">(leave blank to keep current)</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block">Assign Roles</label>
                        @foreach($roles as $role)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('roles') is-invalid @enderror" type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->name }}" {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                            </div>
                        @endforeach
                        @error('roles')
                            <div class="text-danger mt-1" style="font-size: 0.875em;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
