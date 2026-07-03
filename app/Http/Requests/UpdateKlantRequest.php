<?php

namespace App\Http\Requests;

use App\Repositories\KlantRepository;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * Server-side validatie voor klant wijzigen formulier.
 */
class UpdateKlantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'naam' => ['required', 'string', 'max:150'],
            'contact_email' => ['required', 'email', 'max:255'],
            'straatnaam' => ['required', 'string', 'max:150'],
            'huisnummer' => ['required', 'string', 'max:10'],
            'toevoeging' => ['nullable', 'string', 'max:10'],
            'postcode' => ['required', 'string', 'max:10', 'regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/'],
            'plaats' => ['required', 'string', 'max:100'],
            'mobiel' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-()]{10,}$/'],
            'bijzonderheden' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'naam.required' => 'Naam is verplicht.',
            'contact_email.required' => 'Contact e-mail is verplicht.',
            'contact_email.email' => 'Voer een geldig e-mailadres in.',
            'straatnaam.required' => 'Straatnaam is verplicht.',
            'huisnummer.required' => 'Huisnummer is verplicht.',
            'postcode.required' => 'Postcode is verplicht.',
            'postcode.regex' => 'Voer een geldige Nederlandse postcode in.',
            'plaats.required' => 'Plaats is verplicht.',
            'mobiel.required' => 'Mobiel nummer is verplicht.',
            'mobiel.regex' => 'Voer een geldig mobiel nummer in.',
        ];
    }

    /**
     * Extra validatie: uniek contact e-mailadres via stored procedure.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validatorInstance): void {
            if ($validatorInstance->errors()->isNotEmpty()) {
                return;
            }

            $klantRepository = app(KlantRepository::class);
            $klantId = (int) $this->route('klant');
            $klant = $klantRepository->getKlantById($klantId);

            if ($klant === null) {
                return;
            }

            if ($klantRepository->isContactEmailInUse(
                (string) $this->input('contact_email'),
                (int) $klant['ContactId']
            )) {
                $validatorInstance->errors()->add(
                    'contact_email',
                    'Het e-mailadres is al in gebruik'
                );
            }
        });
    }

    /**
     * Toont gebruikersmelding bij mislukte validatie (punt 11).
     */
    protected function failedValidation(Validator $validator): void
    {
        session()->flash('error', 'Klantgegevens zijn niet bijgewerkt.');

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
