@extends('layouts.admin')

@section('title', 'Customer Profile - ' . $customer->name)
@section('page_title', 'Customer Profile & Medical Records')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Profile Summary Card -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; font-size: 1.8rem; font-weight: 700;">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <h4 class="fw-bold mb-1">{{ $customer->name }}</h4>
                <p class="text-muted mb-2"><i class="fa-solid fa-phone me-1"></i>{{ $customer->mobile }}</p>
                <span class="badge bg-{{ $customer->status === 'active' ? 'success' : 'warning' }} mb-3">
                    {{ ucfirst($customer->status) }} Customer
                </span>
                
                <hr>
                
                <div class="text-start">
                    <p class="mb-2"><strong><i class="fa-solid fa-envelope me-2 text-muted"></i>Email:</strong> {{ $customer->email ?: 'N/A' }}</p>
                    <p class="mb-2"><strong><i class="fa-solid fa-calendar me-2 text-muted"></i>Age:</strong> {{ $customer->age ? $customer->age . ' years' : 'N/A' }}</p>
                    <p class="mb-2"><strong><i class="fa-solid fa-venus-mars me-2 text-muted"></i>Gender:</strong> {{ $customer->gender ?: 'N/A' }}</p>
                    <p class="mb-2"><strong><i class="fa-solid fa-droplet me-2 text-danger"></i>Blood Group:</strong> {{ $customer->blood_group ?: 'N/A' }}</p>
                    <p class="mb-2"><strong><i class="fa-solid fa-location-dot me-2 text-muted"></i>Address:</strong> {{ $customer->address ?: 'N/A' }}</p>
                    <p class="mb-2"><strong><i class="fa-solid fa-shield-halved me-2 text-warning"></i>Emergency Contact:</strong> {{ $customer->emergency_contact_name ?: 'N/A' }} {{ $customer->emergency_contact_phone ? '(' . $customer->emergency_contact_phone . ')' : '' }}</p>
                    <p class="mb-0"><strong><i class="fa-solid fa-clock me-2 text-muted"></i>Registered:</strong> {{ $customer->created_at->format('M d, Y h:i A') }}</p>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-primary btn-sm me-2">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Profile
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side: Vitals & Bookings -->
    <div class="col-md-8">
        
        <!-- Lab Metrics Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold"><i class="fa-solid fa-heart-pulse text-danger me-2"></i>Lab Metrics & Vitals</h5>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="collapse" data-bs-target="#addVitalForm">
                    <i class="fa-solid fa-plus me-1"></i> Add Lab Metric
                </button>
            </div>
            <div class="card-body">
                <!-- Collapsible Form to Add Lab Metric -->
                <div class="collapse mb-4" id="addVitalForm">
                    <div class="p-3 bg-light rounded border">
                        <h6 class="fw-bold mb-3">Add New Metric Record</h6>
                        <form method="POST" action="{{ route('admin.customers.vitals.store', $customer->id) }}">
                            @csrf
                            <div class="row g-2 mb-2">
                                <div class="col-md-4">
                                    <input type="text" name="metric_name" class="form-control form-control-sm" placeholder="Metric Name (e.g. Haemoglobin)" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="metric_value" class="form-control form-control-sm" placeholder="Value (e.g. 14.2)" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="unit" class="form-control form-control-sm" placeholder="Unit (e.g. g/dL)">
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select form-select-sm" required>
                                        <option value="Normal">Normal</option>
                                        <option value="High">High</option>
                                        <option value="Low">Low</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-8">
                                    <input type="text" name="normal_range" class="form-control form-control-sm" placeholder="Normal Range (e.g. 13.5 - 17.5)">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-sm btn-danger w-100"><i class="fa-solid fa-save me-1"></i> Save Metric</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Vitals Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Metric Name</th>
                                <th>Result Value</th>
                                <th>Normal Range</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vitals as $vital)
                                <tr>
                                    <td class="fw-bold">{{ $vital->metric_name }}</td>
                                    <td><span class="fs-6 fw-bold">{{ $vital->metric_value }}</span> <small class="text-muted">{{ $vital->unit }}</small></td>
                                    <td class="text-muted small">{{ $vital->normal_range ?: 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $vital->status === 'Normal' ? 'success' : ($vital->status === 'High' ? 'danger' : 'warning') }}">
                                            {{ $vital->status }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.customers.vitals.destroy', $vital->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this metric?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete Metric"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted small">
                                        No lab metrics recorded yet for this customer. Click <strong>"Add Lab Metric"</strong> above to record metrics.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Booking History & Report Upload Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="m-0 fw-bold"><i class="fa-solid fa-calendar-check text-primary me-2"></i>Booking History & Upload Reports</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Booking #</th>
                                <th>Tests / Items</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Medical Report File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td class="fw-bold">
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-decoration-none">
                                            {{ $booking->booking_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="small">{{ $booking->lineItemsLabel() }}</span>
                                        @php $items = $booking->lineItemsForDisplay(); @endphp
                                        @if(count($items) > 1)
                                            <small class="text-muted d-block">{{ count($items) }} items</small>
                                        @endif
                                    </td>
                                    <td>{{ $booking->booking_date ? $booking->booking_date->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="fw-bold">₹{{ $booking->total_price }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($booking->report_file)
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ asset('storage/' . $booking->report_file) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                    <i class="fa-solid fa-file-pdf me-1"></i> View Report
                                                </a>
                                            </div>
                                        @else
                                            <form action="{{ route('admin.bookings.report.upload', $booking->id) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                                                @csrf
                                                <input type="file" name="report" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required style="max-width: 180px;">
                                                <button type="submit" class="btn btn-sm btn-primary" title="Upload PDF Report"><i class="fa-solid fa-upload"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-receipt fs-3 d-block mb-2"></i>
                                        No bookings found for this customer.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
