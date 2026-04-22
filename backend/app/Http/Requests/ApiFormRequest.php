<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class ApiFormRequest extends FormRequest
{
    /**
     * Strip HTML tags from every string value before validation runs.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = $this->sanitizeInput($this->all());
        $this->replace($sanitized);
    }

    private function sanitizeInput(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = strip_tags(trim($value));
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizeInput($value);
            }
        }

        return $data;
    }
}
