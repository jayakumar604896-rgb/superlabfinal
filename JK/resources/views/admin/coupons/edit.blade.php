@extends('layouts.admin')

@section('title', 'Edit Coupon')
@section('page_title', 'Edit Coupon')

@section('content')
<form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
    @csrf
    @method('PUT')
    @include('admin.coupons._form')
    <div class="d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('admin.coupons.show', $coupon->id) }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary px-4">
            <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
        </button>
    </div>
</form>
@endsection

@section('scripts')
@include('admin.coupons._form_scripts')
@endsection
