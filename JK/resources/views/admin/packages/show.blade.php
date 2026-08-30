@extends('layouts.admin')

@section('title', 'Package Details')
@section('page_title', 'Package Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">Package: {{ $package->name }}</h5>
                <div>
                    <a href="{{ route('admin.packages.index') }}" class="btn btn-sm btn-outline-secondary me-2">Back to List</a>
                    @if(!$package->trashed())
                        <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-sm btn-primary">Edit Package</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Brand-like Check Header -->
                <div class="border rounded p-4 bg-light mb-4 position-relative overflow-hidden">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            @if($package->badge)
                                <span class="badge bg-danger text-uppercase px-3 py-2 mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">{{ $package->badge }}</span>
                            @endif
                            <h3 class="fw-bold text-dark mb-1">{{ $package->name }}</h3>
                            <p class="text-secondary mb-0"><i class="fa-solid fa-list-check text-info"></i> Includes <strong>{{ $package->tests_included_count }}</strong> parameters</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            @if($package->discount_percentage)
                                <span class="badge bg-warning text-dark px-3 py-2 mb-2 fs-6">{{ $package->discount_percentage }}% OFF</span>
                            @endif
                            <div class="mb-0">
                                <span class="text-decoration-line-through text-muted fs-5">₹{{ number_format($package->original_price) }}</span>
                                <span class="fw-bold text-success fs-2 ms-2">₹{{ number_format($package->offer_price) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <h6 class="fw-bold text-secondary mb-2">Short Summary</h6>
                    <p class="text-dark bg-white border p-3 rounded" style="line-height: 1.6;">{{ $package->description ?? 'No description provided.' }}</p>
                </div>

                <!-- Components included -->
                <div class="mb-4">
                    <h6 class="fw-bold text-secondary mb-3">Parameters Included ({{ !empty($package->test_components) ? count($package->test_components) : 0 }})</h6>
                    <div class="row g-2">
                        @if(!empty($package->test_components) && is_array($package->test_components))
                            @foreach($package->test_components as $comp)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-2 bg-white border rounded">
                                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                                        <span>{{ $comp }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12 text-muted">No components listed.</div>
                        @endif
                    </div>
                </div>

                <!-- FAQs -->
                <div class="mb-4">
                    <h6 class="fw-bold text-secondary mb-3">Frequently Asked Questions (FAQs)</h6>
                    <div class="accordion" id="faqAccordion">
                        @if(!empty($package->faqs) && is_array($package->faqs))
                            @foreach($package->faqs as $index => $faq)
                                <div class="accordion-item shadow-sm border mb-2 rounded overflow-hidden">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button collapsed fw-semibold text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                            {{ $faq['question'] ?? 'No Question' }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body bg-white border-top text-secondary">
                                            {{ $faq['answer'] ?? 'No Answer' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-muted">No FAQs configured.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
