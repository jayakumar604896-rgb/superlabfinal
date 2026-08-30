@extends('layouts.admin')

@section('title', 'Add Health Check Package')
@section('page_title', 'Add Package')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <form action="{{ route('admin.packages.store') }}" method="POST">
            @csrf

            <!-- General Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="m-0 fw-bold">Package General Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Package Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="badge" class="form-label">Highlight Tag / Badge <span class="text-muted">(Optional)</span></label>
                            <input type="text" class="form-control @error('badge') is-invalid @enderror" id="badge" name="badge" value="{{ old('badge') }}" placeholder="e.g. MOST BOOKED">
                            @error('badge')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="tests_included_count" class="form-label">Number of Tests Included</label>
                            <input type="number" class="form-control @error('tests_included_count') is-invalid @enderror" id="tests_included_count" name="tests_included_count" value="{{ old('tests_included_count', 0) }}">
                            @error('tests_included_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="discount_percentage" class="form-label">Discount Percentage (%)</label>
                            <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" id="discount_percentage" name="discount_percentage" value="{{ old('discount_percentage') }}" placeholder="e.g. 49">
                            @error('discount_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="original_price" class="form-label">Original Price (₹)</label>
                            <input type="number" class="form-control @error('original_price') is-invalid @enderror" id="original_price" name="original_price" value="{{ old('original_price') }}">
                            @error('original_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="offer_price" class="form-label">Offer Price / Sale Price (₹)</label>
                            <input type="number" class="form-control @error('offer_price') is-invalid @enderror" id="offer_price" name="offer_price" value="{{ old('offer_price') }}">
                            @error('offer_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Short Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="fasting_condition" class="form-label">Fasting Condition / Requirements</label>
                        <input type="text" class="form-control @error('fasting_condition') is-invalid @enderror" id="fasting_condition" name="fasting_condition" value="{{ old('fasting_condition') }}" placeholder="e.g. 10-12 Hours Fasting Recommended">
                        @error('fasting_condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Dynamic Components / Test items -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold">Test Components Included</h5>
                    <button type="button" id="addComponent" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-plus"></i> Add Test Parameter</button>
                </div>
                <div class="card-body">
                    <div id="componentsWrapper">
                        <div class="row g-2 mb-2 align-items-center component-item">
                            <div class="col-10">
                                <input type="text" name="test_components[]" class="form-control" placeholder="e.g. Hemogram (24 parameters)">
                            </div>
                            <div class="col-2 text-end">
                                <button type="button" class="btn btn-outline-danger remove-component w-100"><i class="fa-solid fa-trash-can"></i> Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic FAQs -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold">Frequently Asked Questions (FAQs)</h5>
                    <button type="button" id="addFaq" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-plus"></i> Add FAQ</button>
                </div>
                <div class="card-body">
                    <div id="faqsWrapper">
                        <div class="border p-3 rounded mb-3 faq-item position-relative">
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Question</label>
                                <input type="text" name="faqs[0][question]" class="form-control" placeholder="e.g. Is fasting required?">
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Answer</label>
                                <textarea name="faqs[0][answer]" class="form-control" rows="2" placeholder="e.g. Yes, fasting of 10-12 hours is mandatory."></textarea>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-faq position-absolute" style="top: 10px; right: 10px;"><i class="fa-solid fa-xmark"></i> Remove</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="text-end mb-5">
                <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save Package</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Slug generation
    document.getElementById('name').addEventListener('input', function() {
        let name = this.value;
        let slug = name.toLowerCase()
                        .replace(/[^a-z0-9 -]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
        document.getElementById('slug').value = slug;
    });

    // Dynamic Components
    document.getElementById('addComponent').addEventListener('click', function() {
        const wrapper = document.getElementById('componentsWrapper');
        const newItem = document.createElement('div');
        newItem.className = 'row g-2 mb-2 align-items-center component-item';
        newItem.innerHTML = `
            <div class="col-10">
                <input type="text" name="test_components[]" class="form-control" placeholder="e.g. Liver Function Test">
            </div>
            <div class="col-2 text-end">
                <button type="button" class="btn btn-outline-danger remove-component w-100"><i class="fa-solid fa-trash-can"></i> Remove</button>
            </div>
        `;
        wrapper.appendChild(newItem);
    });

    document.getElementById('componentsWrapper').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-component') || e.target.parentElement.classList.contains('remove-component')) {
            const item = e.target.closest('.component-item');
            if (item) item.remove();
        }
    });

    // Dynamic FAQs
    let faqIndex = 1;
    document.getElementById('addFaq').addEventListener('click', function() {
        const wrapper = document.getElementById('faqsWrapper');
        const newItem = document.createElement('div');
        newItem.className = 'border p-3 rounded mb-3 faq-item position-relative';
        newItem.innerHTML = `
            <div class="mb-2">
                <label class="form-label fw-semibold">Question</label>
                <input type="text" name="faqs[${faqIndex}][question]" class="form-control" placeholder="Question text">
            </div>
            <div class="mb-0">
                <label class="form-label fw-semibold">Answer</label>
                <textarea name="faqs[${faqIndex}][answer]" class="form-control" rows="2" placeholder="Answer text"></textarea>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-faq position-absolute" style="top: 10px; right: 10px;"><i class="fa-solid fa-xmark"></i> Remove</button>
        `;
        wrapper.appendChild(newItem);
        faqIndex++;
    });

    document.getElementById('faqsWrapper').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-faq') || e.target.parentElement.classList.contains('remove-faq')) {
            const item = e.target.closest('.faq-item');
            if (item) item.remove();
        }
    });
</script>
@endsection
