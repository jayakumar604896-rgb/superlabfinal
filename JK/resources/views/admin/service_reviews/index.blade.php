@extends('layouts.admin')

@section('title', 'Test & Package Reviews')
@section('page_title', 'Test & Package Reviews')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">Customer Reviews</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.service-reviews.index') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search reviewer or message..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All statuses</option>
                    <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="rejected" {{ $statusFilter == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Reviewer</th>
                        <th>Item</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td class="fw-semibold">{{ $item->reviewer_name }}</td>
                            <td>
                                @if($item->service)
                                    <span class="badge bg-info text-dark">Test</span> {{ $item->service->title }}
                                @elseif($item->package)
                                    <span class="badge bg-primary">Package</span> {{ $item->package->name }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-star {{ $i <= $item->rating ? 'fa-solid text-warning' : 'fa-regular text-muted' }}" style="font-size: 0.8rem;"></i>
                                @endfor
                            </td>
                            <td>
                                <span class="badge {{ $item->status === 'active' ? 'bg-success' : ($item->status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>{{ $item->created_at?->format('d M Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.service-reviews.show', $item->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No reviews found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $records->links() }}
    </div>
</div>
@endsection
