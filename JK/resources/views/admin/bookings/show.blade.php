@extends('layouts.admin')

@section('title', 'Booking Details')
@section('page_title', 'Booking Details')

@section('content')
@php
    $rawItems = $booking->lineItemsForDisplay();
    $notesData = [];
    if (!empty($booking->notes)) {
        foreach (explode(' | ', $booking->notes) as $part) {
            $kv = explode(': ', $part, 2);
            if (count($kv) === 2) {
                $notesData[trim($kv[0])] = trim($kv[1]);
            }
        }
    }
@endphp
<div class="row">
    <div class="col-md-7">
        <!-- Booking details -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Booking Reference: {{ $booking->booking_number }}</h5>
                <div>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary me-1">Back</a>
                    @if(!$booking->trashed())
                        <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <tr>
                        <th class="bg-light w-30">Booking Status</th>
                        <td>
                            @php
                                $statusBadge = 'bg-secondary';
                                if ($booking->status === 'completed') $statusBadge = 'bg-success';
                                elseif ($booking->status === 'cancelled') $statusBadge = 'bg-danger';
                            @endphp
                            <span class="badge {{ $statusBadge }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Payment Status</th>
                        <td>
                            @php
                                $payBadge = 'bg-secondary';
                                if ($booking->payment_status === 'paid') $payBadge = 'bg-success';
                                elseif ($booking->payment_status === 'failed') $payBadge = 'bg-danger';
                                elseif ($booking->payment_status === 'refunded') $payBadge = 'bg-warning text-dark';
                            @endphp
                            <span class="badge {{ $payBadge }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Package Booked</th>
                        <td>
                            @if($booking->package)
                                <span class="fw-bold">{{ $booking->package->name }}</span>
                                <br><small class="text-muted">{{ $booking->package->tests_included_count }} tests included</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Booking Date</th>
                        <td>{{ $booking->booking_date ? $booking->booking_date->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Payment Type</th>
                        <td>{{ $booking->paymentType ? $booking->paymentType->name : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Subtotal</th>
                        <td>₹{{ number_format($booking->subtotal_amount ?? $booking->total_price) }}</td>
                    </tr>
                    @if($booking->discount_amount)
                    <tr>
                        <th class="bg-light">Coupon</th>
                        <td>{{ $booking->coupon_code }} (-₹{{ number_format($booking->discount_amount) }})</td>
                    </tr>
                    @endif
                    <tr>
                        <th class="bg-light">Total Price</th>
                        <td class="fw-bold text-dark">₹{{ number_format($booking->total_price) }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Created At</th>
                        <td>{{ $booking->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Notes</th>
                        <td style="white-space: pre-wrap;">
                            @if($booking->notes && !str_starts_with($booking->notes, 'Address:'))
                                {{ $booking->notes }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Booked Tests/Packages -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="m-0 fw-bold"><i class="fa-solid fa-flask text-primary me-2"></i>Booked Tests/Packages</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Item Name</th>
                                <th>Category</th>
                                <th class="pe-3 text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rawItems as $item)
                                <tr>
                                    <td class="ps-3 fw-bold">{{ $item['name'] }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            @if(($item['type'] ?? '') === 'package')
                                                Health Package
                                            @elseif(!empty($item['category']))
                                                {{ $item['category'] }}
                                            @else
                                                Individual Test
                                            @endif
                                        </span>
                                    </td>
                                    <td class="pe-3 text-end fw-bold">₹{{ number_format($item['price']) }}</td>
                                </tr>
                            @empty
                                @if($booking->package)
                                    <tr>
                                        <td class="ps-3 fw-bold">{{ $booking->package->name }}</td>
                                        <td><span class="badge bg-light text-dark">Health Package</span></td>
                                        <td class="pe-3 text-end fw-bold">₹{{ number_format($booking->total_price) }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">No specific tests or packages listed.</td>
                                    </tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <!-- Customer details -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="m-0 fw-bold">Customer Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th class="bg-light w-40">Name</th>
                        <td>{{ $booking->customer_name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Email</th>
                        <td><a href="mailto:{{ $booking->customer_email }}">{{ $booking->customer_email }}</a></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Phone</th>
                        <td>{{ $booking->customer_phone ?? 'N/A' }}</td>
                    </tr>
                    @if(isset($notesData['Age']))
                    <tr>
                        <th class="bg-light">Age</th>
                        <td>{{ $notesData['Age'] }} Years</td>
                    </tr>
                    @endif
                    @if(isset($notesData['Gender']))
                    <tr>
                        <th class="bg-light">Gender</th>
                        <td>{{ $notesData['Gender'] }}</td>
                    </tr>
                    @endif
                    @if(isset($notesData['Address']))
                    <tr>
                        <th class="bg-light">Collection Address</th>
                        <td>{{ $notesData['Address'] }}</td>
                    </tr>
                    @endif
                    @if(isset($notesData['Payment']))
                    <tr>
                        <th class="bg-light">Payment Method</th>
                        <td><span class="badge bg-light text-dark fw-bold">{{ $notesData['Payment'] }}</span></td>
                    </tr>
                    @endif
                    <tr>
                        <th class="bg-light">Customer Account</th>
                        <td>
                            @if($booking->customer)
                                <a href="{{ route('admin.customers.show', $booking->customer->id) }}" class="fw-semibold text-decoration-none">
                                    <i class="fa-solid fa-user-check text-success"></i> {{ $booking->customer->name }}
                                </a>
                                <br><small class="text-muted">{{ $booking->customer->mobile }}</small>
                            @else
                                <span class="badge bg-secondary">Guest / Unlinked</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Admin User</th>
                        <td>
                            @if($booking->user)
                                <i class="fa-solid fa-user text-primary"></i> {{ $booking->user->name }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Payments log details -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Transactions / Payments</h5>
                @if(!$booking->trashed())
                    <a href="{{ route('admin.payments.create', ['booking_id' => $booking->id]) }}" class="btn btn-xs btn-outline-primary">
                        <i class="fa-solid fa-plus"></i> Record Payment
                    </a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Txn ID</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->payments as $pay)
                                <tr>
                                    <td class="ps-3">
                                        <a href="{{ route('admin.payments.show', $pay->id) }}" class="fw-semibold">
                                            {{ $pay->transaction_id ?? 'Txn-' . $pay->id }}
                                        </a>
                                    </td>
                                    <td>{{ $pay->paymentType ? $pay->paymentType->name : 'N/A' }}</td>
                                    <td class="fw-bold">₹{{ number_format($pay->amount) }}</td>
                                    <td>
                                        <span class="badge {{ $pay->status === 'success' ? 'bg-success' : ($pay->status === 'failed' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                            {{ ucfirst($pay->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No payments recorded yet.</td>
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
