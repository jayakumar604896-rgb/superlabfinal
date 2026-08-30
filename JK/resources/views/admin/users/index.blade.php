@extends('layouts.admin')

@section('title', 'Users Management')
@section('page_title', 'Users Management')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Users (Trash)' : 'Active Users' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-users"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.users.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-user-plus"></i> Add User
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <!-- Search and Filters Form -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 mb-4">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter by Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $roleFilter == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if($search || $roleFilter)
                <div class="col-md-2">
                    <a href="{{ route('admin.users.index', ['trashed' => $withTrashed ? 'true' : 'false']) }}" class="btn btn-outline-danger w-100">Clear Filters</a>
                </div>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>
                            <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort_by' => 'id', 'sort_order' => $sortOrder === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                ID {!! $sortBy === 'id' ? ($sortOrder === 'asc' ? '▲' : '▼') : '' !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_order' => $sortOrder === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                Name {!! $sortBy === 'name' ? ($sortOrder === 'asc' ? '▲' : '▼') : '' !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort_by' => 'email', 'sort_order' => $sortOrder === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                Email {!! $sortBy === 'email' ? ($sortOrder === 'asc' ? '▲' : '▼') : '' !!}
                            </a>
                        </th>
                        <th>Roles</th>
                        <th>
                            <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_order' => $sortOrder === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                Created At {!! $sortBy === 'created_at' ? ($sortOrder === 'asc' ? '▲' : '▼') : '' !!}
                            </a>
                        </th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="fw-medium">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-secondary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                @if($user->trashed())
                                    <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                    <form action="{{ route('admin.users.force-delete', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-circle-minus"></i> Delete</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Soft delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
