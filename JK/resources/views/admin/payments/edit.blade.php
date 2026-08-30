@extends('layouts.admin')

@section('title', 'Edit Payment Record')
@section('page_title', 'Edit Payment Record')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Edit Payment</h5>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payments.update', $payment->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="booking_id" class="form-label">Booking Reference</label>
                        <select class="form-select @error('booking_id') is-invalid @enderror" id="booking_id" name="booking_id">
                            @foreach($bookings as $bk)
                                <option value="{{ $bk->id }}" data-price="{{ $bk->total_price }}" {{ old('booking_id', $payment->booking_id) == $bk->id ? 'selected' : '' }}>
                                    {{ $bk->booking_number }} - {{ $bk->customer_name }} (Total: ₹{{ number_format($bk->total_price) }})
                                </option>
                            @endforeach
                        </select>
                        @error('booking_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payment_type_id" class="form-label">Payment Method</label>
                            <select class="form-select @error('payment_type_id') is-invalid @enderror" id="payment_type_id" name="payment_type_id">
                                @foreach($paymentTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('payment_type_id', $payment->payment_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="transaction_id" class="form-label">Transaction Reference ID (Optional)</label>
                            <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" id="transaction_id" name="transaction_id" value="{{ old('transaction_id', $payment->transaction_id) }}">
                            @error('transaction_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Amount Paid (₹)</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $payment->amount) }}">
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Payment Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="success" {{ old('status', $payment->status) == 'success' ? 'selected' : '' }}>Success</option>
                                <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ old('status', $payment->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ old('status', $payment->status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date & Time</label>
                        <input type="datetime-local" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', $payment->payment_date ? $payment->payment_date->format('Y-m-d\TH:i') : '') }}">
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="remarks" class="form-label">Remarks / Description</label>
                        <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3">{{ old('remarks', $payment->remarks) }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update Payment Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
