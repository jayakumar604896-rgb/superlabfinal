<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        return [
            'title' => 'nullable|string|max:255',
            'image' => ($isUpdate ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:active,inactive',
        ];
    }
}
