@extends('layouts.admin')

@section('title', 'Enquiry Details')
@section('page_title', 'Enquiry Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">From: {{ $enquiry->name }}</h5>
                <a href="{{ route('admin.enquiries.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th class="bg-light w-25">Sender Name</th>
                        <td class="fw-semibold">{{ $enquiry->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Email Address</th>
                        <td><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Mobile Number</th>
                        <td>{{ $enquiry->mobile }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Subject</th>
                        <td>{{ $enquiry->subject ?? 'No Subject' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Received At</th>
                        <td>{{ $enquiry->created_at->format('Y-m-d H:i:s') }} ({{ $enquiry->created_at->diffForHumans() }})</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Status</th>
                        <td>
                            <span class="badge {{ $enquiry->status === 'read' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ucfirst($enquiry->status) }}
                            </span>
                        </td>
                    </tr>
                </table>

                <div class="mt-4 p-4 border rounded bg-white">
                    <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">Enquiry Message</h6>
                    <p style="white-space: pre-wrap; line-height: 1.6;">{{ $enquiry->message }}</p>
                </div>

                <div class="text-end mt-4">
                    <a href="mailto:{{ $enquiry->email }}?subject=RE: {{ rawurlencode($enquiry->subject) }}" class="btn btn-primary"><i class="fa-solid fa-reply"></i> Reply by Email</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
