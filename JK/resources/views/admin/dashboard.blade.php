@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Overview')

@section('content')
<div class="row">
    <!-- Metric Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 border-start border-primary border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Users</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $metrics['users'] }}</div>
                    </div>
                    <div class="text-primary opacity-50">
                        <i class="fa-solid fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 border-start border-success border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Services</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $metrics['services'] }}</div>
                    </div>
                    <div class="text-success opacity-50">
                        <i class="fa-solid fa-stethoscope fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 border-start border-info border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Blogs</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $metrics['blogs'] }}</div>
                    </div>
                    <div class="text-info opacity-50">
                        <i class="fa-solid fa-newspaper fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 border-start border-warning border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Enquiries</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $metrics['enquiries'] }}</div>
                    </div>
                    <div class="text-warning opacity-50">
                        <i class="fa-solid fa-envelope-open-text fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Enquiries -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fa-solid fa-message"></i> Recent Contact Enquiries</h5>
                @can('manage enquiries')
                <a href="{{ route('admin.enquiries.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEnquiries as $enquiry)
                            <tr>
                                <td class="fw-medium">{{ $enquiry->name }}</td>
                                <td>{{ Str::limit($enquiry->subject, 30) }}</td>
                                <td>
                                    <span class="badge {{ $enquiry->status === 'read' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($enquiry->status) }}
                                    </span>
                                </td>
                                <td>
                                    @can('manage enquiries')
                                    <a href="{{ route('admin.enquiries.show', $enquiry->id) }}" class="btn btn-xs btn-primary py-0 px-2" style="font-size: 0.8rem;">Read</a>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No recent enquiries found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logs -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="m-0 font-weight-bold text-dark"><i class="fa-solid fa-clock-rotate-left text-secondary"></i> Recent System Activity Logs</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($recentLogs as $log)
                    <div class="list-group-item px-0">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <h6 class="mb-1 fw-semibold text-primary" style="font-size: 0.9rem;">
                                {{ $log->action }}
                            </h6>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                {{ $log->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <p class="mb-1 text-muted" style="font-size: 0.85rem;">
                            {{ $log->description }}
                        </p>
                        <small class="text-secondary" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-user-circle"></i> {{ $log->user ? $log->user->name : 'System' }} | <i class="fa-solid fa-network-wired"></i> {{ $log->ip_address }}
                        </small>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">No activity logs recorded.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
