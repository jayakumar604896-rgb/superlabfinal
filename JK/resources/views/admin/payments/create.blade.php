@extends('layouts.admin')

@section('title', 'Record Payment')
@section('page_title', 'Record Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Payment Details</h5>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payments.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="booking_id" class="form-label">Select Booking</label>
                        <select class="form-select @error('booking_id') is-invalid @enderror" id="booking_id" name="booking_id">
                            <option value="">-- Choose Booking --</option>
                            @foreach($bookings as $bk)
                                <option value="{{ $bk->id }}" data-price="{{ $bk->total_price }}" {{ old('booking_id', $selectedBookingId) == $bk->id ? 'selected' : '' }}>
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
                                <option value="">-- Choose Method --</option>
                                @foreach($paymentTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('payment_type_id') == $type->id ? 'selected' : '' }}>
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
                            <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}" placeholder="e.g. TXN10293029">
                            @error('transaction_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Amount Paid (₹)</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}">
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Payment Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="success" {{ old('status', 'success') == 'success' ? 'selected' : '' }}>Success</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ old('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date & Time</label>
                        <input type="datetime-local" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d\TH:i')) }}">
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="remarks" class="form-label">Remarks / Description</label>
                        <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save Payment Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Autofill the amount based on booking total price
    document.getElementById('booking_id').addEventListener('change', function() {
        let selectedOption = this.options[this.selectedIndex];
        let price = selectedOption.getAttribute('data-price');
        if (price) {
            document.getElementById('amount').value = price;
        } else {
            document.getElementById('amount').value = '';
        }
    });
</script>
@endsection
