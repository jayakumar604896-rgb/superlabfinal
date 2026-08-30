<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name' => 'required|string|max:255',
            'client_designation' => 'nullable|string|max:255',
            'message' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'client_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:active,inactive',
        ];
    }
}
