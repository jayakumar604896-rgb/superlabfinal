@extends('layouts.admin')

@section('title', 'User Details')
@section('page_title', 'User Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">User: {{ $user->name }}</h5>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$user->trashed())
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">Edit User</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <tr>
                        <th class="w-25 bg-light">ID</th>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Name</th>
                        <td class="fw-semibold">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Email Address</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Assigned Roles</th>
                        <td>
                            @forelse($user->roles as $role)
                                <span class="badge bg-secondary">{{ $role->name }}</span>
                            @empty
                                <span class="text-muted">No roles assigned.</span>
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Status</th>
                        <td>
                            @if($user->trashed())
                                <span class="badge bg-danger">Soft Deleted (Archived)</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Created At</th>
                        <td>{{ $user->created_at->format('Y-m-d H:i:s') }} ({{ $user->created_at->diffForHumans() }})</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Updated At</th>
                        <td>{{ $user->updated_at->format('Y-m-d H:i:s') }} ({{ $user->updated_at->diffForHumans() }})</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
