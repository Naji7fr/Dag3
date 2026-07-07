<?php

namespace App\Http\Requests;

use App\Services\DutchAddressValidator;
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
                    if ($value === null || $value === '') {
                        return;
                    }

                    if (! DutchAddressValidator::isValidPostcode((string) $value)) {
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
