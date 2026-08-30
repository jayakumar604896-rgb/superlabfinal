@extends('layouts.admin')

@section('title', 'Create Coupon')
@section('page_title', 'Create Coupon')

@section('content')
<form action="{{ route('admin.coupons.store') }}" method="POST">
    @csrf
    @include('admin.coupons._form')
    <div class="d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary px-4">
            <i class="fa-solid fa-check me-1"></i> Create Coupon
        </button>
    </div>
</form>
@endsection

@section('scripts')
@include('admin.coupons._form_scripts')
@endsection
