<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validatie voor het filteren van medewerkers op specialisatie.
 * Implementeert PSR-12 en Laravel best practices.
 */
class MedewerkerFilterRequest extends FormRequest
{
    /**
     * Bepaalt of de user deze request mag aanroepen.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isEigenaar();
    }

    /**
     * Validatieregels voor het filteren van medewerkers.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'specialisatie' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Custom foutmeldingen voor validatie.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'specialisatie.string' => 'De specialisatie moet een tekst zijn.',
            'specialisatie.max' => 'De specialisatie mag maximaal 100 karakters zijn.',
            'page.integer' => 'Pagina moet een getal zijn.',
            'page.min' => 'Pagina moet minimaal 1 zijn.',
        ];
    }
}
