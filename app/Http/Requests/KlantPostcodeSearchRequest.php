<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Server-side validatie voor postcode zoeken op klantenoverzicht.
 */
class KlantPostcodeSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $postcode = $this->input('postcode');

        if (is_string($postcode) && $postcode !== '') {
            $this->merge([
                'postcode' => strtoupper(str_replace(' ', '', trim($postcode))),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'postcode' => [
                'nullable',
                'string',
                'max:10',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $pattern = config('kniploket.postcode_pattern');

                    if ($value !== null && $value !== '' && ! preg_match($pattern, (string) $value)) {
                        $fail('Voer een geldige Nederlandse postcode in (bijv. 3512AB).');
                    }
                },
            ],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'postcode.regex' => 'Voer een geldige Nederlandse postcode in (bijv. 3512AB).',
        ];
    }
}
