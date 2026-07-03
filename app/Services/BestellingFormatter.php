<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Hulpfuncties voor bestellingweergave.
 */
class BestellingFormatter
{
    /**
     * @param array<string, mixed> $bestellingRecord
     */
    public static function formatKlantNaam(array $bestellingRecord): string
    {
        return KlantFormatter::formatNaam([
            'Voornaam' => $bestellingRecord['Voornaam'] ?? '',
            'Tussenvoegsel' => $bestellingRecord['Tussenvoegsel'] ?? '',
            'Achternaam' => $bestellingRecord['Achternaam'] ?? '',
        ]);
    }

    public static function formatDatum(mixed $datum): string
    {
        if ($datum === null || $datum === '') {
            return '';
        }

        return Carbon::parse((string) $datum)->format('d-m-Y');
    }

    public static function formatTijd(mixed $tijd): string
    {
        if ($tijd === null || $tijd === '') {
            return '';
        }

        return substr((string) $tijd, 0, 5);
    }
}
