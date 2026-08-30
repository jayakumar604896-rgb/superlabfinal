<?php

namespace App\Http\Requests\Admin;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        foreach (Setting::all() as $setting) {
            $rules[$setting->key] = $this->rulesForSetting($setting);
        }

        return $rules;
    }

    private function rulesForSetting(Setting $setting): array
    {
        return match ($setting->type) {
            'file' => ['nullable', 'file', 'image', 'max:2048'],
            'textarea' => ['nullable', 'string', 'max:5000'],
            default => match (true) {
                str_contains($setting->key, 'email') => ['nullable', 'email', 'max:255'],
                str_starts_with($setting->key, 'social_') => ['nullable', 'url', 'max:500'],
                default => ['nullable', 'string', 'max:255'],
            },
        };
    }
}
