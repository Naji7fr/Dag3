<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Validatie voor het updaten van medewerker gegevens.
 * Implementeert PSR-12 en Laravel best practices.
 * Validatie: minderjarige medewerkers kunnen geen Permanent specialisatie krijgen.
 */
class UpdateMedewerkerRequest extends FormRequest
{
    /**
     * Bepaalt of de user deze request mag aanroepen.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isEigenaar();
    }

    /**
     * Validatieregels voor het updaten van medewerker.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'voornaam' => ['required', 'string', 'max:100'],
            'tussenvoegsel' => ['nullable', 'string', 'max:50'],
            'achternaam' => ['required', 'string', 'max:100'],
            'specialisatie' => ['required', 'string', 'max:100'],
            'geboortedatum' => ['required', 'date_format:Y-m-d'],
            'straatnaam' => ['required', 'string', 'max:150'],
            'huisnummer' => ['required', 'string', 'max:10'],
            'toevoeging' => ['nullable', 'string', 'max:10'],
            'postcode' => ['required', 'string', 'max:10'],
            'plaats' => ['required', 'string', 'max:100'],
            'contact_email' => ['required', 'email', 'max:255'],
            'mobiel' => ['required', 'string', 'max:20'],
            'opmerking' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Hook na standaardvalidatie voor custom regels.
     * Controleer of minderjarige medewerker "Permanent" krijgt toegewezen.
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $specialisatie = $this->input('specialisatie');
            $geboortedatum = $this->input('geboortedatum');

            // Controleer of minderjarige medewerker "Permanent" krijgt toegewezen
            if ($specialisatie === 'Permanent' && $geboortedatum) {
                $leeftijd = Carbon::createFromFormat('Y-m-d', $geboortedatum)->age;
                if ($leeftijd < 18) {
                    $validator->errors()->add(
                        'specialisatie',
                        'Minderjarige medewerkers mogen geen specialisatie Permanent toegewezen krijgen vanwege het werken met gevaarlijke stoffen en chemicaliën.'
                    );
                }
            }
        });
    }

    /**
     * Custom foutmeldingen voor validatie.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'voornaam.required' => 'Voornaam is verplicht.',
            'voornaam.string' => 'Voornaam moet een tekst zijn.',
            'voornaam.max' => 'Voornaam mag maximaal 100 karakters zijn.',
            'achternaam.required' => 'Achternaam is verplicht.',
            'achternaam.string' => 'Achternaam moet een tekst zijn.',
            'achternaam.max' => 'Achternaam mag maximaal 100 karakters zijn.',
            'specialisatie.required' => 'Specialisatie is verplicht.',
            'specialisatie.string' => 'Specialisatie moet een tekst zijn.',
            'specialisatie.max' => 'Specialisatie mag maximaal 100 karakters zijn.',
            'geboortedatum.required' => 'Geboortedatum is verplicht.',
            'geboortedatum.date_format' => 'Geboortedatum moet in format YYYY-MM-DD zijn.',
            'straatnaam.required' => 'Straatnaam is verplicht.',
            'huisnummer.required' => 'Huisnummer is verplicht.',
            'postcode.required' => 'Postcode is verplicht.',
            'plaats.required' => 'Plaats is verplicht.',
            'contact_email.required' => 'Contact e-mail is verplicht.',
            'contact_email.email' => 'Contact e-mail moet een geldig e-mailadres zijn.',
            'mobiel.required' => 'Mobiel is verplicht.',
        ];
    }
}
