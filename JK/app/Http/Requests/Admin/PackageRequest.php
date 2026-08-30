<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\SanitizesDynamicFields;
use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    use SanitizesDynamicFields;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('package') ? $this->route('package') : null;

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:packages,slug,' . $id,
            'badge' => 'nullable|string|max:100',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'tests_included_count' => 'required|integer|min:0',
            'original_price' => 'required|integer|min:0',
            'offer_price' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'test_components' => 'nullable|array',
            'test_components.*' => 'nullable|string|max:255',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'required_with:faqs.*.answer|string|max:255',
            'faqs.*.answer' => 'required_with:faqs.*.question|string',
            'fasting_condition' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ];
    }
}
