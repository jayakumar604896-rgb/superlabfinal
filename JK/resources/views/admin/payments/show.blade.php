@extends('layouts.admin')

@section('title', 'Payment Record Details')
@section('page_title', 'Payment Record Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Transaction Reference: {{ $payment->transaction_id ?? 'Txn-' . $payment->id }}</h5>
                <div>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$payment->trashed())
                        <a href="{{ route('admin.payments.edit', $payment->id) }}" class="btn btn-sm btn-primary">Edit Record</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <tr>
                        <th class="bg-light w-30">Transaction ID</th>
                        <td class="fw-semibold">{{ $payment->transaction_id ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Booking Reference</th>
                        <td>
                            @if($payment->booking)
                                <a href="{{ route('admin.bookings.show', $payment->booking->id) }}" class="fw-bold">
                                    {{ $payment->booking->booking_number }}
                                </a>
                                <br><small class="text-muted">Customer: {{ $payment->booking->customer_name }}</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Payment Method</th>
                        <td>{{ $payment->paymentType ? $payment->paymentType->name : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Amount</th>
                        <td class="fw-bold text-success">₹{{ number_format($payment->amount) }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Payment Status</th>
                        <td>
                            <span class="badge {{ $payment->status === 'success' ? 'bg-success' : ($payment->status === 'failed' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Payment Date & Time</th>
                        <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y h:i A') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Remarks / Description</th>
                        <td style="white-space: pre-wrap;">{{ $payment->remarks ?? 'No remarks recorded.' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Created At</th>
                        <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Last Updated</th>
                        <td>{{ $payment->updated_at->format('M d, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
