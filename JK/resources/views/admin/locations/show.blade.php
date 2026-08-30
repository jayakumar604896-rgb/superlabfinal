@extends('layouts.admin')

@section('title', 'Location Details')
@section('page_title', 'Location Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">{{ $location->name }}</h5>
                <div>
                    <a href="{{ route('admin.locations.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$location->trashed())
                        <a href="{{ route('admin.locations.edit', $location->id) }}" class="btn btn-sm btn-primary">Edit Location</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th class="bg-light w-25">Location Name</th>
                        <td class="fw-semibold">{{ $location->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Slug</th>
                        <td><code>{{ $location->slug }}</code></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Phone Number</th>
                        <td>{{ $location->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Email Address</th>
                        <td>
                            @if($location->email)
                                <a href="mailto:{{ $location->email }}">{{ $location->email }}</a>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Physical Address</th>
                        <td style="white-space: pre-wrap;">{{ $location->address }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Status</th>
                        <td>
                            <span class="badge {{ $location->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($location->status) }}
                            </span>
                        </td>
                    </tr>
                </table>

                @if($location->map_iframe)
                    <div class="mt-4">
                        <h6 class="fw-bold text-secondary mb-2">Google Map Location</h6>
                        <div class="border rounded overflow-hidden">
                            {!! $location->map_iframe !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
