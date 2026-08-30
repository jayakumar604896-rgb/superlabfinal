@extends('layouts.admin')

@section('title', 'Global Settings')
@section('page_title', 'System Settings')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="m-0 fw-bold"><i class="fa-solid fa-gears"></i> Website Configurations</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
                    @foreach($settings as $group => $items)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-capitalize {{ $loop->first ? 'active' : '' }}" id="{{ $group }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $group }}" type="button" role="tab" aria-controls="{{ $group }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $group }} Settings
                            </button>
                        </li>
                    @endforeach
                </ul>

                <!-- Form -->
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="tab-content" id="settingsTabContent">
                        @foreach($settings as $group => $items)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $group }}" role="tabpanel" aria-labelledby="{{ $group }}-tab">
                                <div class="p-3">
                                    @foreach($items as $setting)
                                        <div class="mb-3 row">
                                            <label for="setting_{{ $setting->key }}" class="col-sm-3 col-form-label fw-medium text-capitalize">
                                                {{ str_replace('_', ' ', str_replace($group . '_', '', $setting->key)) }}
                                            </label>
                                            <div class="col-sm-9">
                                                @if($setting->type === 'textarea')
                                                    <textarea class="form-control @error($setting->key) is-invalid @enderror" id="setting_{{ $setting->key }}" name="{{ $setting->key }}" rows="4">{{ old($setting->key, $setting->value) }}</textarea>
                                                    @error($setting->key)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                @elseif($setting->type === 'file')
                                                    @if($setting->value)
                                                        <div class="mb-2">
                                                            <img src="{{ asset($setting->value) }}" alt="Logo" class="img-thumbnail" style="max-height: 50px;">
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control @error($setting->key) is-invalid @enderror" id="setting_{{ $setting->key }}" name="{{ $setting->key }}">
                                                    @error($setting->key)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                @else
                                                    <input type="text" class="form-control @error($setting->key) is-invalid @enderror" id="setting_{{ $setting->key }}" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}">
                                                    @error($setting->key)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-end border-top pt-3 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Configurations</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
