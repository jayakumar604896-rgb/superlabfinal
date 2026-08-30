@extends('layouts.admin')

@section('title', 'Testimonials Management')
@section('page_title', 'Testimonials Management')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Testimonials (Trash)' : 'Testimonials List' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-star-half-stroke"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.testimonials.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
                <a href="{{ route('admin.testimonials.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Testimonial
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.testimonials.index') }}" class="row g-3 mb-4">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search client name..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter by Status</option>
                    <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $statusFilter == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 10%;">Avatar</th>
                        <th style="width: 20%;">Client Name</th>
                        <th style="width: 15%;">Designation</th>
                        <th style="width: 10%;">Rating</th>
                        <th style="width: 10%;">Status</th>
                        <th class="text-end" style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                @if($item->client_image)
                                    <img src="{{ asset($item->client_image) }}" alt="{{ $item->client_name }}" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px; font-size: 0.95rem;">
                                        {{ substr($item->client_name, 0, 1) }}
                                    </div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $item->client_name }}</td>
                            <td>{{ $item->client_designation ?? 'N/A' }}</td>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-star {{ $i <= $item->rating ? 'fa-solid text-warning' : 'fa-regular text-muted' }}" style="font-size: 0.8rem;"></i>
                                @endfor
                            </td>
                            <td>
                                <span class="badge {{ $item->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($item->trashed())
                                    <form action="{{ route('admin.testimonials.restore', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                    <form action="{{ route('admin.testimonials.force-delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this testimonial?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-circle-minus"></i> Delete</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.testimonials.show', $item->id) }}" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('admin.testimonials.edit', $item->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('admin.testimonials.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Soft delete this testimonial?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No testimonials found.</td>
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
