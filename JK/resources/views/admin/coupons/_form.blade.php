@php
    $selectedCustomers = old('customer_ids', isset($coupon) ? $coupon->customers->pluck('id')->all() : []);
    $discountType = old('discount_type', isset($coupon) ? $coupon->discount_type : 'percent');
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Basic details --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="fa-solid fa-ticket me-2"></i>Coupon details</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="coupon_code" class="form-label">Coupon code <span class="text-danger">*</span></label>
                        <input type="text" id="coupon_code" name="code"
                               class="form-control text-uppercase fw-semibold @error('code') is-invalid @enderror"
                               value="{{ old('code', isset($coupon) ? $coupon->code : '') }}"
                               placeholder="e.g. SUPER25" autocomplete="off">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <label for="coupon_name" class="form-label">Display name</label>
                        <input type="text" id="coupon_name" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', isset($coupon) ? $coupon->name : '') }}"
                               placeholder="Shown internally in CRM">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="coupon_status" class="form-label">Status</label>
                        <select id="coupon_status" name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="active" @selected(old('status', isset($coupon) ? $coupon->status : 'active') === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', isset($coupon) ? $coupon->status : 'active') === 'inactive')>Inactive</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Discount rules --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="fa-solid fa-percent me-2"></i>Discount rules</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="discount_type" class="form-label">Discount type</label>
                        <select id="discount_type" name="discount_type" class="form-select @error('discount_type') is-invalid @enderror">
                            <option value="percent" @selected($discountType === 'percent')>Percentage off</option>
                            <option value="fixed" @selected($discountType === 'fixed')>Fixed amount off</option>
                        </select>
                        @error('discount_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="discount_value" class="form-label">Discount value <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text discount-prefix">{{ $discountType === 'fixed' ? '₹' : '%' }}</span>
                            <input type="number" step="0.01" min="0.01" id="discount_value" name="discount_value"
                                   class="form-control @error('discount_value') is-invalid @enderror"
                                   value="{{ old('discount_value', isset($coupon) ? $coupon->discount_value : '') }}"
                                   placeholder="{{ $discountType === 'fixed' ? '500' : '25' }}">
                            @error('discount_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="min_order_amount" class="form-label">Minimum order</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" min="0" id="min_order_amount" name="min_order_amount"
                                   class="form-control @error('min_order_amount') is-invalid @enderror"
                                   value="{{ old('min_order_amount', isset($coupon) ? $coupon->min_order_amount : '') }}"
                                   placeholder="No minimum">
                            @error('min_order_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-text">Leave blank for no minimum cart value.</div>
                    </div>
                    <div class="col-md-4" id="max_discount_wrap">
                        <label for="max_discount_amount" class="form-label">Max discount cap</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" min="0" id="max_discount_amount" name="max_discount_amount"
                                   class="form-control @error('max_discount_amount') is-invalid @enderror"
                                   value="{{ old('max_discount_amount', isset($coupon) ? $coupon->max_discount_amount : '') }}"
                                   placeholder="Unlimited">
                            @error('max_discount_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-text">Applies to percentage coupons only.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="usage_limit" class="form-label">Total usage limit</label>
                        <input type="number" min="1" id="usage_limit" name="usage_limit"
                               class="form-control @error('usage_limit') is-invalid @enderror"
                               value="{{ old('usage_limit', isset($coupon) ? $coupon->usage_limit : '') }}"
                               placeholder="Unlimited">
                        @error('usage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if(isset($coupon))
                            <div class="form-text">Used {{ $coupon->usage_count }} time(s) so far.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Validity --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="fa-regular fa-calendar me-2"></i>Validity period</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="starts_at" class="form-label">Valid from</label>
                        <input type="datetime-local" id="starts_at" name="starts_at"
                               class="form-control @error('starts_at') is-invalid @enderror"
                               value="{{ old('starts_at', isset($coupon?->starts_at) ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
                        @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Optional — starts immediately if empty.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="expires_at" class="form-label">Valid until</label>
                        <input type="datetime-local" id="expires_at" name="expires_at"
                               class="form-control @error('expires_at') is-invalid @enderror"
                               value="{{ old('expires_at', isset($coupon?->expires_at) ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
                        @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Optional — never expires if empty.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Audience --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="fa-solid fa-users me-2"></i>Who can use this coupon</h6>
            </div>
            <div class="card-body">
                <div class="p-3 rounded border bg-light mb-4">
                    <div class="form-check form-switch mb-0">
                        <input type="checkbox" class="form-check-input" role="switch" id="guest_eligible"
                               name="guest_eligible" value="1"
                               @checked(old('guest_eligible', isset($coupon) ? $coupon->guest_eligible : false))>
                        <label class="form-check-label fw-semibold" for="guest_eligible">Allow guest checkout</label>
                    </div>
                    <p class="text-muted small mb-0 mt-2">
                        Guests can apply this code without logging in. Logged-in customers who are not specifically assigned fall back to this same rule.
                    </p>
                    @error('guest_eligible')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <label class="form-label d-flex justify-content-between align-items-center">
                    <span>Assign to specific customers <span class="text-muted fw-normal">(optional)</span></span>
                    <span class="badge bg-primary" id="customer-selected-count">{{ count($selectedCustomers) }} selected</span>
                </label>

                @if($customers->isEmpty())
                    <div class="alert alert-warning mb-0">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        No customers in CRM yet. Enable guest checkout or add customers first.
                    </div>
                @else
                    <div class="coupon-customer-picker border rounded bg-white">
                        <div class="p-2 border-bottom bg-light">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="search" id="customer-search" class="form-control"
                                       placeholder="Search by name, mobile, or email…" autocomplete="off">
                            </div>
                        </div>
                        <div class="coupon-customer-list p-2" id="customer-list">
                            @foreach($customers as $customer)
                                @php
                                    $isSelected = in_array($customer->id, $selectedCustomers);
                                    $searchText = strtolower(trim($customer->name . ' ' . $customer->mobile . ' ' . ($customer->email ?? '')));
                                @endphp
                                <label class="coupon-customer-item d-flex align-items-start gap-2 p-2 rounded mb-1 {{ $isSelected ? 'is-selected' : '' }}"
                                       data-search="{{ $searchText }}">
                                    <input type="checkbox" class="form-check-input mt-1 customer-checkbox flex-shrink-0"
                                           name="customer_ids[]" value="{{ $customer->id }}"
                                           @checked($isSelected)>
                                    <span class="flex-grow-1">
                                        <span class="fw-semibold d-block">{{ $customer->name }}</span>
                                        <span class="text-muted small">{{ $customer->mobile }}@if($customer->email) · {{ $customer->email }}@endif</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <div class="p-2 border-top bg-light d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" id="customer-select-all">Select all visible</button>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-muted" id="customer-clear-all">Clear all</button>
                        </div>
                    </div>
                    <div class="form-text mt-2">
                        Account-only coupons: disable guest checkout and assign customers here. Assigned customers can always use the code even if guest checkout is off.
                    </div>
                @endif
                @error('customer_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                @error('customer_ids.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    {{-- Live preview --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 coupon-preview-card sticky-top" style="top: 1rem;">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold"><i class="fa-regular fa-eye me-2"></i>Preview</h6>
            </div>
            <div class="card-body">
                <div class="text-center py-3 mb-3 rounded border border-dashed bg-light">
                    <div class="display-6 fw-bold text-primary mb-1" id="preview-code">{{ old('code', isset($coupon) ? $coupon->code : 'NEWCODE') }}</div>
                    <div class="text-muted" id="preview-name">{{ old('name', isset($coupon) ? ($coupon->name ?: 'Coupon name') : 'Coupon name') }}</div>
                </div>

                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Discount</span>
                        <span class="fw-semibold" id="preview-discount">—</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Min. order</span>
                        <span id="preview-min-order">None</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Audience</span>
                        <span id="preview-audience">—</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Usage</span>
                        <span id="preview-usage">Unlimited</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Validity</span>
                        <span class="text-end" id="preview-validity">Always active</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .coupon-customer-list {
        max-height: 260px;
        overflow-y: auto;
    }
    .coupon-customer-item {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .coupon-customer-item:hover {
        background-color: #f8fafc;
    }
    .coupon-customer-item.is-selected {
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
    }
    .coupon-customer-item.hidden-by-search {
        display: none !important;
    }
    .coupon-preview-card .border-dashed {
        border-style: dashed !important;
    }
</style>
