<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Server-side validatie voor statusfilter op bestellingenoverzicht.
 */
class BestellingStatusFilterRequest extends FormRequest
{
    public const STATUSSEN = [
        'Ontvangen',
        'Bevestigd',
        'Inverwerking',
        'Verzonden',
        'Afgeleverd',
        'Geannuleerd',
    ];

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
            'status' => ['nullable', 'string', Rule::in(self::STATUSSEN)],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
