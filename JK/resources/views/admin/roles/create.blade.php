@extends('layouts.admin')

@section('title', 'Add New Role')
@section('page_title', 'Add New Role')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Role Information</h5>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Content Manager">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block fw-bold">Assign Permissions</label>
                        <div class="row">
                            @foreach($permissions as $permission)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->name }}" {{ is_array(old('permissions')) && in_array($permission->name, old('permissions')) ? 'checked' : '' }}>
                                        <label class="form-check-label text-capitalize" for="permission_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
