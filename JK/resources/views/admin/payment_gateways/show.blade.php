@extends('layouts.admin')

@section('title', 'Payment Gateway Details')
@section('page_title', 'Payment Gateway Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">{{ $gateway->name }} Credentials</h5>
                <div>
                    <a href="{{ route('admin.payment-gateways.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$gateway->trashed())
                        <a href="{{ route('admin.payment-gateways.edit', $gateway->id) }}" class="btn btn-sm btn-primary">Edit Gateway</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <tr>
                        <th class="bg-light w-30">Gateway Name</th>
                        <td class="fw-bold text-dark">{{ $gateway->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Slug</th>
                        <td><code>{{ $gateway->slug }}</code></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Environment</th>
                        <td>
                            <span class="badge {{ $gateway->environment === 'live' ? 'bg-danger' : 'bg-info text-dark' }}">
                                {{ strtoupper($gateway->environment) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Status</th>
                        <td>
                            <span class="badge {{ $gateway->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($gateway->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">API Key / Client ID</th>
                        <td>
                            @if($gateway->api_key)
                                <code>{{ substr($gateway->api_key, 0, 15) }}... [Hidden for Security]</code>
                            @else
                                <span class="text-muted">Not Configured</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">API Secret / Secret Key</th>
                        <td>
                            @if($gateway->api_secret)
                                <code>[Configured - Hidden for Security]</code>
                            @else
                                <span class="text-muted">Not Configured</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Webhook Secret</th>
                        <td>
                            @if($gateway->webhook_secret)
                                <code>[Configured - Hidden for Security]</code>
                            @else
                                <span class="text-muted">Not Configured</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Created At</th>
                        <td>{{ $gateway->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Last Updated</th>
                        <td>{{ $gateway->updated_at->format('M d, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
