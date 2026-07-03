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

    /**
     * @param array<string, mixed> $productRegel
     */
    public static function berekenTotaal(array $productRegel, ?int $aantal = null): float
    {
        $effectiefAantal = $aantal ?? (int) ($productRegel['Aantal'] ?? 0);
        $unitPrijs = (float) ($productRegel['UnitPrijs'] ?? 0);
        $korting = (float) ($productRegel['Korting'] ?? 0);
        $btwPercentage = (float) ($productRegel['BTWPercentage'] ?? 0);

        $subtotaal = ($unitPrijs * $effectiefAantal) - $korting;
        $btw = $subtotaal * ($btwPercentage / 100);

        return round($subtotaal + $btw, 2);
    }

    public static function formatEuro(float $bedrag): string
    {
        return 'EUR '.number_format($bedrag, 2, ',', '.');
    }

    /**
     * @param array<string, mixed> $productRegel
     */
    public static function formatTotaal(array $productRegel, ?int $aantal = null): string
    {
        return self::formatEuro(self::berekenTotaal($productRegel, $aantal));
    }
}
