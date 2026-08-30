@extends('layouts.admin')

@section('title', 'Contact Enquiries')
@section('page_title', 'Contact Enquiries')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">{{ $withTrashed ? 'Archived Enquiries (Trash)' : 'Enquiries List' }}</h5>
        <div>
            @if($withTrashed)
                <a href="{{ route('admin.enquiries.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fa-solid fa-envelope-open-text"></i> View Active
                </a>
            @else
                <a href="{{ route('admin.enquiries.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-danger me-2">
                    <i class="fa-solid fa-trash-can"></i> View Trash
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.enquiries.index') }}" class="row g-3 mb-4">
            <input type="hidden" name="trashed" value="{{ $withTrashed ? 'true' : 'false' }}">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search enquiries..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter by Status</option>
                    <option value="unread" {{ $statusFilter == 'unread' ? 'selected' : '' }}>Unread</option>
                    <option value="read" {{ $statusFilter == 'read' ? 'selected' : '' }}>Read</option>
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 15%;">Sender</th>
                        <th style="width: 15%;">Mobile</th>
                        <th style="width: 15%;">Email</th>
                        <th style="width: 20%;">Subject</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 15%;">Received At</th>
                        <th class="text-end" style="width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td class="fw-semibold">{{ $item->name }}</td>
                            <td>{{ $item->mobile }}</td>
                            <td><a href="mailto:{{ $item->email }}">{{ $item->email }}</a></td>
                            <td>{{ Str::limit($item->subject, 40) }}</td>
                            <td>
                                <span class="badge {{ $item->status === 'read' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                @if($item->trashed())
                                    <form action="{{ route('admin.enquiries.restore', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                    <form action="{{ route('admin.enquiries.force-delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this enquiry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-circle-minus"></i> Delete</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.enquiries.show', $item->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-envelope-open"></i> Read</a>
                                    <form action="{{ route('admin.enquiries.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Soft delete this enquiry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No enquiries found.</td>
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
