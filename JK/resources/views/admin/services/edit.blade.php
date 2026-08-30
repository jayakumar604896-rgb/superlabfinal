@extends('layouts.admin')

@section('title', 'Edit Test')
@section('page_title', 'Edit Test')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Edit Test Information</h5>
                <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $service->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Test Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $service->title) }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $service->slug) }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="original_price" class="form-label">Original Price (₹) <span class="text-muted">(Optional)</span></label>
                            <input type="number" class="form-control @error('original_price') is-invalid @enderror" id="original_price" name="original_price" value="{{ old('original_price', $service->original_price) }}" placeholder="MRP before discount">
                            @error('original_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Offer Price (₹)</label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $service->price) }}">
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="2">{{ old('short_description', $service->short_description) }}</textarea>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Full Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Service Image</label>
                        @if($service->image)
                            <div class="mb-2">
                                <img src="{{ asset($service->image) }}" alt="{{ $service->title }}" class="img-thumbnail" style="width: 100px;">
                            </div>
                        @endif
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="home_collection_available" name="home_collection_available" value="1" {{ old('home_collection_available', $service->home_collection_available) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="home_collection_available">
                                <i class="fa-solid fa-house-medical text-primary me-1"></i> Home Sample Collection Available
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="popular" name="popular" value="1" {{ old('popular', $service->popular) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="popular">
                                <i class="fa-solid fa-fire text-warning me-1"></i> Mark as Popular Test
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fasting_condition" class="form-label">Fasting Condition / Requirements</label>
                        <input type="text" class="form-control @error('fasting_condition') is-invalid @enderror" id="fasting_condition" name="fasting_condition" value="{{ old('fasting_condition', $service->fasting_condition) }}" placeholder="e.g. No Fasting Required">
                        @error('fasting_condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="active" {{ old('status', $service->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $service->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dynamic Components / Test items -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <h6 class="m-0 fw-bold">Test Parameters Included</h6>
                            <button type="button" id="addComponent" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-plus"></i> Add Parameter</button>
                        </div>
                        <div class="card-body">
                            <div id="componentsWrapper">
                                @if(!empty($service->test_components) && is_array($service->test_components))
                                    @foreach($service->test_components as $comp)
                                        <div class="row g-2 mb-2 align-items-center component-item">
                                            <div class="col-10">
                                                <input type="text" name="test_components[]" class="form-control" value="{{ $comp }}" placeholder="e.g. Parameter value">
                                            </div>
                                            <div class="col-2 text-end">
                                                <button type="button" class="btn btn-outline-danger remove-component w-100"><i class="fa-solid fa-trash-can"></i> Remove</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row g-2 mb-2 align-items-center component-item">
                                        <div class="col-10">
                                            <input type="text" name="test_components[]" class="form-control" placeholder="e.g. Quantitative analysis">
                                        </div>
                                        <div class="col-2 text-end">
                                            <button type="button" class="btn btn-outline-danger remove-component w-100"><i class="fa-solid fa-trash-can"></i> Remove</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic FAQs -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <h6 class="m-0 fw-bold">Frequently Asked Questions (FAQs)</h6>
                            <button type="button" id="addFaq" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-plus"></i> Add FAQ</button>
                        </div>
                        <div class="card-body">
                            <div id="faqsWrapper">
                                @if(!empty($service->faqs) && is_array($service->faqs))
                                    @foreach($service->faqs as $index => $faq)
                                        <div class="border p-3 rounded mb-3 faq-item position-relative">
                                            <div class="mb-2">
                                                <label class="form-label fw-semibold">Question</label>
                                                <input type="text" name="faqs[{{ $index }}][question]" class="form-control" value="{{ $faq['question'] ?? '' }}" placeholder="e.g. Is fasting required?">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-semibold">Answer</label>
                                                <textarea name="faqs[{{ $index }}][answer]" class="form-control" rows="2" placeholder="e.g. Yes, fasting of 10-12 hours is mandatory.">{{ $faq['answer'] ?? '' }}</textarea>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-faq position-absolute" style="top: 10px; right: 10px;"><i class="fa-solid fa-xmark"></i> Remove</button>
                                        </div>
                                    @endforeach
                                @else
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
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update Test</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('title').addEventListener('input', function() {
        let title = this.value;
        let slug = title.toLowerCase()
                        .replace(/[^a-z0-9 -]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
        document.getElementById('slug').value = slug;
    });

    // Dynamic components
    document.getElementById('addComponent').addEventListener('click', function() {
        const wrapper = document.getElementById('componentsWrapper');
        const newItem = document.createElement('div');
        newItem.className = 'row g-2 mb-2 align-items-center component-item';
        newItem.innerHTML = `
            <div class="col-10">
                <input type="text" name="test_components[]" class="form-control" placeholder="e.g. Parameter value">
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
    let faqIndex = {{ !empty($service->faqs) ? count($service->faqs) : 1 }};
    document.getElementById('addFaq').addEventListener('click', function() {
        const wrapper = document.getElementById('faqsWrapper');
        const newItem = document.createElement('div');
        newItem.className = 'border p-3 rounded mb-3 faq-item position-relative';
        newItem.innerHTML = `
            <div class="mb-2">
                <label class="form-label fw-semibold">Question</label>
                <input type="text" name="faqs[${faqIndex}][question]" class="form-control" placeholder="e.g. Is fasting required?">
            </div>
            <div class="mb-0">
                <label class="form-label fw-semibold">Answer</label>
                <textarea name="faqs[${faqIndex}][answer]" class="form-control" rows="2" placeholder="e.g. Yes, fasting of 10-12 hours is mandatory."></textarea>
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
