<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreServiceReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => 'nullable|integer|exists:services,id',
            'service_slug' => 'nullable|string|max:255',
            'package_id' => 'nullable|integer|exists:packages,id',
            'package_slug' => 'nullable|string|max:255',
            'reviewer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'required|string|min:10|max:5000',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $hasService = $this->filled('service_id') || $this->filled('service_slug');
            $hasPackage = $this->filled('package_id') || $this->filled('package_slug');

            if ($hasService && $hasPackage) {
                $validator->errors()->add('service_id', 'Review must be for either a test or a package, not both.');
            }

            if (! $hasService && ! $hasPackage) {
                $validator->errors()->add('service_id', 'Specify which test or package you are reviewing.');
            }
        });
    }
}
