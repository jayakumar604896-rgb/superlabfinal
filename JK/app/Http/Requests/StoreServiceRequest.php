<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\SanitizesDynamicFields;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    use SanitizesDynamicFields;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:services,slug',
            'short_description' => 'nullable|string|max:1000',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|integer|min:0|gte:price',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:active,inactive',
            'home_collection_available' => 'nullable|boolean',
            'popular' => 'nullable|boolean',
            'fasting_condition' => 'nullable|string|max:255',
            'test_components' => 'nullable|array',
            'test_components.*' => 'nullable|string|max:255',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'required_with:faqs.*.answer|string|max:255',
            'faqs.*.answer' => 'required_with:faqs.*.question|string',
        ];
    }
}
