@extends('layouts.admin')

@section('title', 'Add Booking')
@section('page_title', 'Add New Booking')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Booking Details</h5>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.bookings.store') }}" method="POST">
                    @csrf

                    <h6 class="fw-bold text-primary mb-3">1. Reference & Package Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="booking_number" class="form-label">Booking Number</label>
                            <input type="text" class="form-control @error('booking_number') is-invalid @enderror" id="booking_number" name="booking_number" value="{{ old('booking_number', $bookingNumber) }}" readonly>
                            @error('booking_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="package_id" class="form-label">Select Package (Optional)</label>
                            <select class="form-select @error('package_id') is-invalid @enderror" id="package_id" name="package_id">
                                <option value="">-- Cart / multi-item booking --</option>
                                <option value="">-- Choose Package --</option>
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->id }}" data-price="{{ $pkg->offer_price }}" {{ old('package_id') == $pkg->id ? 'selected' : '' }}>
                                        {{ $pkg->name }} (₹{{ number_format($pkg->offer_price) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('package_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 mt-3">2. Customer Information</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name') }}">
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="customer_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror" id="customer_email" name="customer_email" value="{{ old('customer_email') }}">
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="customer_phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}">
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_id" class="form-label">Link to Customer Account</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                                <option value="">-- Guest / No linked account --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->name }} ({{ $cust->mobile }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">Link to Admin User (Optional)</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                <option value="">-- Guest Booking --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="booking_date" class="form-label">Booking Date</label>
                            <input type="date" class="form-control @error('booking_date') is-invalid @enderror" id="booking_date" name="booking_date" value="{{ old('booking_date', date('Y-m-d')) }}">
                            @error('booking_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 mt-3">3. Payment & Pricing Details</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="payment_type_id" class="form-label">Payment Type</label>
                            <select class="form-select @error('payment_type_id') is-invalid @enderror" id="payment_type_id" name="payment_type_id">
                                <option value="">-- Choose Payment Type --</option>
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

                        <div class="col-md-4 mb-3">
                            <label for="total_price" class="form-label">Total Price (₹)</label>
                            <input type="number" class="form-control @error('total_price') is-invalid @enderror" id="total_price" name="total_price" value="{{ old('total_price') }}">
                            @error('total_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="payment_status" class="form-label">Payment Status</label>
                            <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status">
                                <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ old('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ old('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                            @error('payment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Booking Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="notes" class="form-label">Notes / Remarks</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Automatically set the total price when a package is selected
    document.getElementById('package_id').addEventListener('change', function() {
        let selectedOption = this.options[this.selectedIndex];
        let price = selectedOption.getAttribute('data-price');
        if (price) {
            document.getElementById('total_price').value = price;
        } else {
            document.getElementById('total_price').value = '';
        }
    });
</script>
@endsection
