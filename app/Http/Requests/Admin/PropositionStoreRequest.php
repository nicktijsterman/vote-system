<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property array{horizontal: array<string, ?string>, vertical: array<string, ?string>} options
 */
class PropositionStoreRequest extends FormRequest
{
    use ValidatesPropositions;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string'],
            'order' => ['required', 'integer', 'nullable'],
            'options' => ['required', 'array', 'size:2'],
            'options.horizontal' => ['required', 'array', 'min:1'],
            'options.vertical' => ['required', 'array', 'min:1'],
            'options.*.*' => ['present', 'string'],
        ];
    }

    #[\Override]
    public function prepareForValidation()
    {
        /** @var array{horizontal: array<string, ?string>, vertical: array<string, ?string>} $options */
        $options = $this->options;

        $this->merge([
            'options' => [
                'horizontal' => $this->rejectNullEntries($options['horizontal']),
                'vertical' => $this->rejectNullEntries($options['vertical']),
            ],
        ]);
    }
}
