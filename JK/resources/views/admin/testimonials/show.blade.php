@extends('layouts.admin')

@section('title', 'Testimonial Details')
@section('page_title', 'Testimonial Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Testimonial Detail</h5>
                <div>
                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$testimonial->trashed())
                        <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}" class="btn btn-sm btn-primary">Edit Testimonial</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <tr>
                        <th class="w-25 bg-light">ID</th>
                        <td>{{ $testimonial->id }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Photo</th>
                        <td>
                            @if($testimonial->client_image)
                                <img src="{{ asset($testimonial->client_image) }}" alt="{{ $testimonial->client_name }}" class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <span class="text-muted">No photo uploaded.</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Client Name</th>
                        <td class="fw-semibold">{{ $testimonial->client_name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Designation</th>
                        <td>{{ $testimonial->client_designation ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Rating</th>
                        <td>
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-star {{ $i <= $testimonial->rating ? 'fa-solid text-warning' : 'fa-regular text-muted' }}"></i>
                            @endfor
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Message</th>
                        <td><em>"{{ $testimonial->message }}"</em></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Status</th>
                        <td>
                            <span class="badge {{ $testimonial->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($testimonial->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Created At</th>
                        <td>{{ $testimonial->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
