<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('location') ? $this->route('location') : null;

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:locations,slug,' . $id,
            'address' => 'required|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'map_iframe' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];
    }
}
