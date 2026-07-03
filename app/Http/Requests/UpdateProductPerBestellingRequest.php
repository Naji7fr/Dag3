<?php

namespace App\Http\Requests;

use App\Repositories\BestellingRepository;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * Server-side validatie voor bestelproduct wijzigen formulier.
 */
class UpdateProductPerBestellingRequest extends FormRequest
{
    public const STATUS_AFGELEVERD = 'Afgeleverd';

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
            'aantal' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'aantal.required' => 'Aantal is verplicht.',
            'aantal.integer' => 'Aantal moet een geheel getal zijn.',
            'aantal.min' => 'Aantal moet minimaal 1 zijn.',
        ];
    }

    /**
     * Extra validatie: geen wijziging bij afgeleverde bestelling.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validatorInstance): void {
            if ($validatorInstance->errors()->isNotEmpty()) {
                return;
            }

            $bestellingRepository = app(BestellingRepository::class);
            $productPerBestellingId = (int) $this->route('productPerBestelling');
            $productRegel = $bestellingRepository->getProductPerBestellingById($productPerBestellingId);

            if ($productRegel === null) {
                return;
            }

            if (($productRegel['Bestelstatus'] ?? '') === self::STATUS_AFGELEVERD) {
                $validatorInstance->errors()->add(
                    'aantal',
                    'Aantal kan niet worden gewijzigd omdat de bestelling al is afgeleverd'
                );
            }
        });
    }

    /**
     * Toont gebruikersmelding bij mislukte validatie.
     */
    protected function failedValidation(Validator $validator): void
    {
        session()->flash('error', 'Gegevens zijn niet gewijzigd');

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
