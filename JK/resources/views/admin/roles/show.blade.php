@extends('layouts.admin')

@section('title', 'Role Details')
@section('page_title', 'Role Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Role: {{ $role->name }}</h5>
                <div>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$role->trashed())
                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-primary">Edit Role</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <tr>
                        <th class="w-25 bg-light">ID</th>
                        <td>{{ $role->id }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Role Name</th>
                        <td class="fw-semibold">{{ $role->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Permissions Assigned</th>
                        <td>
                            @forelse($role->permissions as $permission)
                                <span class="badge bg-info text-dark mb-1">{{ $permission->name }}</span>
                            @empty
                                <span class="text-muted">No permissions assigned.</span>
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Status</th>
                        <td>
                            @if($role->trashed())
                                <span class="badge bg-danger">Soft Deleted (Archived)</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Created At</th>
                        <td>{{ $role->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
