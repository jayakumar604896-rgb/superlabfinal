@extends('layouts.admin')

@section('title', 'Footer Location Details')
@section('page_title', 'Footer Location Details')

@section('content')
<div class="card shadow-sm col-md-8 mx-auto">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">Footer Location Details #{{ $location->id }}</h5>
        <div>
            <a href="{{ route('admin.footer-locations.edit', $location->id) }}" class="btn btn-sm btn-primary me-2">
                <i class="fa-solid fa-pen"></i> Edit
            </a>
            <a href="{{ route('admin.footer-locations.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered align-middle">
            <tbody>
                <tr>
                    <th style="width: 30%;" class="table-light">ID</th>
                    <td>{{ $location->id }}</td>
                </tr>
                <tr>
                    <th class="table-light">Location Name</th>
                    <td class="fw-bold">{{ $location->location_name }}</td>
                </tr>
                <tr>
                    <th class="table-light">Google Maps Link</th>
                    <td>
                        @if($location->map_link)
                            <a href="{{ $location->map_link }}" target="_blank" class="text-primary text-decoration-none">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> {{ $location->map_link }}
                            </a>
                        @else
                            <span class="text-muted">Not specified</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="table-light">Status</th>
                    <td>
                        <span class="badge {{ $location->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($location->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th class="table-light">Created At</th>
                    <td>{{ $location->created_at ? $location->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                </tr>
                <tr>
                    <th class="table-light">Updated At</th>
                    <td>{{ $location->updated_at ? $location->updated_at->format('M d, Y h:i A') : 'N/A' }}</td>
                </tr>
                @if($location->trashed())
                <tr>
                    <th class="table-light text-danger">Deleted At</th>
                    <td class="text-danger">{{ $location->deleted_at->format('M d, Y h:i A') }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
