@extends('layouts.admin')

@section('title', 'Edit Payment Gateway')
@section('page_title', 'Edit Payment Gateway')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Edit Gateway: {{ $gateway->name }}</h5>
                <a href="{{ route('admin.payment-gateways.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payment-gateways.update', $gateway->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Gateway Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $gateway->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $gateway->slug) }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="environment" class="form-label">Environment</label>
                            <select class="form-select @error('environment') is-invalid @enderror" id="environment" name="environment">
                                <option value="sandbox" {{ old('environment', $gateway->environment) == 'sandbox' ? 'selected' : '' }}>Sandbox / Testing</option>
                                <option value="live" {{ old('environment', $gateway->environment) == 'live' ? 'selected' : '' }}>Live / Production</option>
                            </select>
                            @error('environment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="inactive" {{ old('status', $gateway->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="active" {{ old('status', $gateway->status) == 'active' ? 'selected' : '' }}>Active</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold text-primary mb-3">Gateway Credentials</h6>

                    <div class="mb-3">
                        <label for="api_key" class="form-label">API Key / Publishable Key / Client ID</label>
                        <input type="text" class="form-control @error('api_key') is-invalid @enderror" id="api_key" name="api_key" value="{{ old('api_key', $gateway->api_key) }}">
                        @error('api_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="api_secret" class="form-label">API Secret / Secret Key</label>
                        <input type="password" class="form-control @error('api_secret') is-invalid @enderror" id="api_secret" name="api_secret" value="{{ old('api_secret', $gateway->api_secret) }}" placeholder="Leave blank to keep current secret or enter new secret">
                        @error('api_secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="webhook_secret" class="form-label">Webhook Signature Secret (Optional)</label>
                        <input type="password" class="form-control @error('webhook_secret') is-invalid @enderror" id="webhook_secret" name="webhook_secret" value="{{ old('webhook_secret', $gateway->webhook_secret) }}" placeholder="Leave blank to keep current signature or enter new signature">
                        @error('webhook_secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update Payment Gateway</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('name').addEventListener('input', function() {
        let name = this.value;
        let slug = name.toLowerCase()
                        .replace(/[^a-z0-9 -]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
        document.getElementById('slug').value = slug;
    });
</script>
@endsection
