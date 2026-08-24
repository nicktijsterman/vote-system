<?php

namespace App\Http\Requests\Admin;

use App\Models\AppConfig;
use Illuminate\Foundation\Http\FormRequest;

class AppConfigUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [];
        foreach (AppConfig::dictionary()->keys() as $name) {
            $rules[$name] = ['present', 'nullable', 'string'];
        }

        return $rules;
    }
}
