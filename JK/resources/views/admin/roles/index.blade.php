@extends('layouts.admin')

@section('title', 'Roles & Permissions')
@section('page_title', 'Roles & Permissions')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Roles (Trash)' : 'Active Roles' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-user-shield"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.roles.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Role
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.roles.index') }}" class="row g-3 mb-4">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search roles..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">ID</th>
                        <th style="width: 25%;">Role Name</th>
                        <th>Permissions</th>
                        <th class="text-end" style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td class="fw-medium">{{ $role->name }}</td>
                            <td>
                                @forelse($role->permissions as $permission)
                                    <span class="badge bg-info text-dark mb-1">{{ $permission->name }}</span>
                                @empty
                                    <span class="text-muted small">No permissions assigned.</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                @if($role->trashed())
                                    <form action="{{ route('admin.roles.restore', $role->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                    @if($role->name !== 'Super Admin')
                                        <form action="{{ route('admin.roles.force-delete', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-circle-minus"></i> Delete</button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen"></i></a>
                                    @if($role->name !== 'Super Admin')
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Soft delete this role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i></button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
