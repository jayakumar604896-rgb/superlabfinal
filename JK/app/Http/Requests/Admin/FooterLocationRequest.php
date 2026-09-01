<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FooterLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location_name' => 'required|string|max:255',
            'map_link' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];
    }
}
