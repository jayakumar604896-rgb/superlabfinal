<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LookupGuestReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $digits = preg_replace('/\D/', '', (string) $this->input('mobile', ''));

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }

        $this->merge(['mobile' => $digits]);
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'digits:10', 'regex:/^[6-9]\d{9}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => 'Please enter your mobile number.',
            'mobile.digits' => 'Enter a valid 10-digit mobile number.',
            'mobile.regex' => 'Enter a valid Indian mobile number.',
        ];
    }
}
