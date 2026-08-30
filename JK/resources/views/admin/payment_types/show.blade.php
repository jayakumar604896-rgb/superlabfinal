@extends('layouts.admin')

@section('title', 'Payment Type Details')
@section('page_title', 'Payment Type Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">{{ $paymentType->name }}</h5>
                <div>
                    <a href="{{ route('admin.payment-types.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$paymentType->trashed())
                        <a href="{{ route('admin.payment-types.edit', $paymentType->id) }}" class="btn btn-sm btn-primary">Edit Payment Type</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th class="bg-light w-25">Payment Type Name</th>
                        <td class="fw-semibold">{{ $paymentType->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Slug</th>
                        <td><code>{{ $paymentType->slug }}</code></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Description</th>
                        <td style="white-space: pre-wrap;">{{ $paymentType->description ?? 'No description provided.' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Status</th>
                        <td>
                            <span class="badge {{ $paymentType->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($paymentType->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Created At</th>
                        <td>{{ $paymentType->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Last Updated</th>
                        <td>{{ $paymentType->updated_at->format('M d, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
