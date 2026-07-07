<?php

namespace App\Services;

/**
 * Validatie voor Nederlandse adresvelden (straatnaam, postcode en plaats).
 */
class DutchAddressValidator
{
    public static function isValidPostcode(string $postcode): bool
    {
        $normalized = KlantFormatter::normalizePostcode($postcode);

        return (bool) preg_match(config('kniploket.postcode_pattern'), $normalized);
    }

    public static function isValidStraatnaam(string $straatnaam): bool
    {
        $straatnaam = trim($straatnaam);

        if ($straatnaam === '' || ! preg_match(config('kniploket.straatnaam_pattern'), $straatnaam)) {
            return false;
        }

        if (self::containsInvalidStraatnaamDigits($straatnaam)) {
            return false;
        }

        if (self::containsRepeatedCharacters($straatnaam)) {
            return false;
        }

        if (! self::hasValidDutchStreetEnding($straatnaam)) {
            return false;
        }

        return ! self::isBlockedAddressWord($straatnaam);
    }

    /**
     * Nederlandse straatnamen eindigen op een herkenbaar straattype (straat, laan, weg, …)
     * of volgen het patroon "Laan van …".
     */
    private static function hasValidDutchStreetEnding(string $straatnaam): bool
    {
        $normalized = mb_strtolower(trim($straatnaam));

        if (preg_match('/^laan van [\p{L}\s\'\-\.]+$/u', $normalized)) {
            return true;
        }

        foreach (config('kniploket.straat_suffixes', []) as $suffix) {
            if (str_ends_with($normalized, $suffix)) {
                return true;
            }
        }

        return false;
    }

    private static function containsRepeatedCharacters(string $straatnaam): bool
    {
        return (bool) preg_match('/(.)\1{2,}/u', $straatnaam);
    }

    /**
     * Huisnummers horen in het huisnummer-veld, niet in de straatnaam.
     * Alleen ordinals zoals "1e" aan het begin zijn toegestaan.
     */
    private static function containsInvalidStraatnaamDigits(string $straatnaam): bool
    {
        if (! preg_match('/\d/u', $straatnaam)) {
            return false;
        }

        if (preg_match('/^\d+e\s+.+/u', $straatnaam)) {
            $naamZonderOrdinal = (string) preg_replace('/^\d+e\s+/u', '', $straatnaam);

            return (bool) preg_match('/\d/u', $naamZonderOrdinal);
        }

        return true;
    }

    public static function isValidPlaats(string $plaats): bool
    {
        $plaats = trim($plaats);

        if ($plaats === '' || ! preg_match(config('kniploket.plaats_pattern'), $plaats)) {
            return false;
        }

        if (self::isBlockedAddressWord($plaats)) {
            return false;
        }

        $normalizedPlaats = mb_strtolower($plaats);

        foreach (self::allowedPlaatsen() as $allowedPlaats) {
            if (mb_strtolower($allowedPlaats) === $normalizedPlaats) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    public static function allowedPlaatsen(): array
    {
        return config('kniploket.dutch_plaatsen', []);
    }

    private static function isBlockedAddressWord(string $value): bool
    {
        $normalized = mb_strtolower(trim($value));

        return in_array($normalized, config('kniploket.blocked_address_words', []), true);
    }
}
