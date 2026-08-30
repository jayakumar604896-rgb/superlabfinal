<?php

namespace App\Http\Requests\Concerns;

trait SanitizesDynamicFields
{
    protected function prepareForValidation(): void
    {
        if ($this->has('test_components') && is_array($this->test_components)) {
            $this->merge([
                'test_components' => array_values(array_filter(
                    $this->test_components,
                    fn ($value) => filled($value)
                )),
            ]);
        }

        if ($this->has('faqs') && is_array($this->faqs)) {
            $faqs = array_values(array_filter(
                $this->faqs,
                function ($item) {
                    if (! is_array($item)) {
                        return false;
                    }

                    return filled($item['question'] ?? null) || filled($item['answer'] ?? null);
                }
            ));

            $this->merge(['faqs' => $faqs]);
        }
    }
}
