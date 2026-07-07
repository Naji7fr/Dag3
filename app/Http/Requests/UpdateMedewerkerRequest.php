<?php

namespace App\Http\Requests;

use App\Services\DutchAddressValidator;
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
     * Validatieregels voor het updaten van medewerker.
     *
     * @return array<string, array<int, mixed|string>>
     */
    public function rules(): array
    {
        $minAge = (int) config('kniploket.medewerker_min_age', 15);
        $maxAge = (int) config('kniploket.medewerker_max_age', 100);
        $oudsteGeboortedatum = now()->subYears($maxAge)->format('Y-m-d');
        $jongsteGeboortedatum = now()->subYears($minAge)->format('Y-m-d');

        return [
            'voornaam' => ['required', 'string', 'max:100'],
            'tussenvoegsel' => ['nullable', 'string', 'max:50'],
            'achternaam' => ['required', 'string', 'max:100'],
            'specialisatie' => ['required', 'string', 'max:100'],
            'geboortedatum' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:'.$oudsteGeboortedatum,
                'before_or_equal:'.$jongsteGeboortedatum,
            ],
            'straatnaam' => [
                'required',
                'string',
                'max:150',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || ! DutchAddressValidator::isValidStraatnaam($value)) {
                        $fail('Voer een geldige Nederlandse straatnaam in (bijv. Oudegracht of Winkel van Sinkelstraat).');
                    }
                },
            ],
            'huisnummer' => [
                'required',
                'string',
                'max:10',
                'regex:'.config('kniploket.huisnummer_pattern'),
            ],
            'toevoeging' => ['nullable', 'string', 'max:10'],
            'postcode' => [
                'required',
                'string',
                'max:10',
                'regex:'.config('kniploket.postcode_pattern'),
            ],
            'plaats' => [
                'required',
                'string',
                'max:100',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || ! DutchAddressValidator::isValidPlaats($value)) {
                        $fail('Voer een geldige Nederlandse plaatsnaam in (bijv. Utrecht).');
                    }
                },
            ],
            'contact_email' => ['required', 'email', 'max:255'],
            'mobiel' => [
                'required',
                'string',
                'max:20',
                'regex:'.config('kniploket.mobiel_pattern'),
            ],
            'opmerking' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Hook na standaardvalidatie voor custom regels.
     * Controleer of minderjarige medewerker "Permanent" krijgt toegewezen.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validatorInstance): void {
            $specialisatie = $this->input('specialisatie');
            $geboortedatum = $this->input('geboortedatum');

            if ($specialisatie === 'Permanent' && $geboortedatum) {
                $leeftijd = Carbon::createFromFormat('Y-m-d', $geboortedatum)->age;
                if ($leeftijd < 18) {
                    $validatorInstance->errors()->add(
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
            'geboortedatum.after_or_equal' => 'Voer een realistische geboortedatum in (medewerker mag maximaal 100 jaar oud zijn).',
            'geboortedatum.before_or_equal' => 'Medewerker moet minimaal 15 jaar oud zijn.',
            'straatnaam.required' => 'Straatnaam is verplicht.',
            'huisnummer.required' => 'Huisnummer is verplicht.',
            'huisnummer.regex' => 'Voer een geldig huisnummer in (bijv. 88 of 12A).',
            'postcode.required' => 'Postcode is verplicht.',
            'postcode.regex' => 'Voer een geldige Nederlandse postcode in (bijv. 3512AB).',
            'plaats.required' => 'Plaats is verplicht.',
            'contact_email.required' => 'Contact e-mail is verplicht.',
            'contact_email.email' => 'Contact e-mail moet een geldig e-mailadres zijn.',
            'mobiel.required' => 'Mobiel is verplicht.',
            'mobiel.regex' => 'Voer een geldig Nederlands mobiel nummer in (bijv. 06XXXXXXXX of +31 6 XXXXXXXX).',
        ];
    }
}
