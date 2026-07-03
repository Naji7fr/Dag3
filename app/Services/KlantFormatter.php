<?php

namespace App\Services;

/**
 * Hulpfuncties voor klantweergave en naamparsing.
 */
class KlantFormatter
{
    /**
     * @param array<string, mixed> $klantRecord
     */
    public static function formatNaam(array $klantRecord): string
    {
        $naamDelen = array_filter([
            $klantRecord['Voornaam'] ?? '',
            $klantRecord['Tussenvoegsel'] ?? '',
            $klantRecord['Achternaam'] ?? '',
        ]);

        return implode(' ', $naamDelen);
    }

    /**
     * @param array<string, mixed> $klantRecord
     */
    public static function formatAdres(array $klantRecord): string
    {
        $adres = ($klantRecord['Straatnaam'] ?? '') . ' ' . ($klantRecord['Huisnummer'] ?? '');

        if (! empty($klantRecord['Toevoeging'])) {
            $adres .= ' ' . $klantRecord['Toevoeging'];
        }

        return trim($adres);
    }

    /**
     * Normaliseert een Nederlandse postcode (hoofdletters, zonder spaties).
     */
    public static function normalizePostcode(string $postcode): string
    {
        return strtoupper(str_replace(' ', '', trim($postcode)));
    }

    /**
     * @param array<string, mixed> $klantRecord
     */
    public static function formatPostcodePlaats(array $klantRecord): string
    {
        $postcode = $klantRecord['Postcode'] ?? '';
        $plaats = $klantRecord['Plaats'] ?? '';

        return trim($postcode.' '.$plaats);
    }

    /**
     * @return array{voornaam: string, tussenvoegsel: string|null, achternaam: string}
     */
    public static function parseNaam(string $volledigeNaam): array
    {
        $naamDelen = preg_split('/\s+/', trim($volledigeNaam)) ?: [];

        if (count($naamDelen) === 0) {
            return ['voornaam' => '', 'tussenvoegsel' => null, 'achternaam' => ''];
        }

        if (count($naamDelen) === 1) {
            return ['voornaam' => $naamDelen[0], 'tussenvoegsel' => null, 'achternaam' => $naamDelen[0]];
        }

        if (count($naamDelen) === 2) {
            return ['voornaam' => $naamDelen[0], 'tussenvoegsel' => null, 'achternaam' => $naamDelen[1]];
        }

        return [
            'voornaam' => $naamDelen[0],
            'tussenvoegsel' => implode(' ', array_slice($naamDelen, 1, -1)),
            'achternaam' => $naamDelen[count($naamDelen) - 1],
        ];
    }

    /**
     * @param array<string, mixed> $klantRecord
     * @return array<string, string>
     */
    public static function formFromRecord(array $klantRecord): array
    {
        return [
            'naam' => self::formatNaam($klantRecord),
            'contact_email' => $klantRecord['ContactEmail'] ?? '',
            'straatnaam' => $klantRecord['Straatnaam'] ?? '',
            'huisnummer' => $klantRecord['Huisnummer'] ?? '',
            'toevoeging' => $klantRecord['Toevoeging'] ?? '',
            'postcode' => $klantRecord['Postcode'] ?? '',
            'plaats' => $klantRecord['Plaats'] ?? '',
            'mobiel' => $klantRecord['Mobiel'] ?? '',
            'bijzonderheden' => $klantRecord['Bijzonderheden'] ?? '',
        ];
    }
}
