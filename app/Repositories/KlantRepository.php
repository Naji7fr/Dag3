<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;
use Throwable;

/**
 * Data-access laag voor klanten via MySQL stored procedures.
 */
class KlantRepository
{
    /**
     * Haalt klanten op via sp_Klant_GetAll (INNER JOINs in stored procedure).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllKlanten(?string $postcode = null): array
    {
        try {
            $resultaten = DB::select('CALL sp_Klant_GetAll(?)', [$postcode ?? '']);
            $klanten = array_map(static fn (object $rij): array => (array) $rij, $resultaten);

            Log::info('sp_Klant_GetAll uitgevoerd.', [
                'postcode' => $postcode,
                'aantal' => count($klanten),
            ]);

            return $klanten;
        } catch (PDOException $exception) {
            Log::error('Fout bij ophalen klanten via stored procedure.', [
                'postcode' => $postcode,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Haalt één klant op via sp_Klant_GetById.
     *
     * @return array<string, mixed>|null
     */
    public function getKlantById(int $klantId): ?array
    {
        try {
            $resultaten = DB::select('CALL sp_Klant_GetById(?)', [$klantId]);
            $klant = $resultaten[0] ?? null;

            if ($klant === null) {
                Log::warning('sp_Klant_GetById leverde geen resultaat.', ['klantId' => $klantId]);
            }

            return $klant !== null ? (array) $klant : null;
        } catch (PDOException $exception) {
            Log::error('Fout bij ophalen klant op id.', [
                'klantId' => $klantId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Controleert via sp_Klant_IsContactEmailInUse of e-mail al in gebruik is.
     */
    public function isContactEmailInUse(string $email, int $contactId): bool
    {
        try {
            $resultaten = DB::select('CALL sp_Klant_IsContactEmailInUse(?, ?)', [$email, $contactId]);

            return ((int) ($resultaten[0]->Aantal ?? 0)) > 0;
        } catch (PDOException $exception) {
            Log::error('Fout bij e-mail uniciteit controle via sp_Klant_IsContactEmailInUse.', [
                'email' => $email,
                'contactId' => $contactId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Werkt klantgegevens bij via sp_Klant_Update.
     *
     * @param array<string, mixed> $klantData
     */
    public function updateKlant(int $klantId, int $contactId, array $klantData): void
    {
        try {
            DB::select('CALL sp_Klant_Update(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $klantId,
                $contactId,
                $klantData['voornaam'],
                $klantData['tussenvoegsel'],
                $klantData['achternaam'],
                $klantData['bijzonderheden'],
                $klantData['straatnaam'],
                $klantData['huisnummer'],
                $klantData['toevoeging'],
                $klantData['postcode'],
                $klantData['plaats'],
                $klantData['contact_email'],
                $klantData['mobiel'],
            ]);

            Log::info('sp_Klant_Update succesvol uitgevoerd.', [
                'klantId' => $klantId,
                'contactId' => $contactId,
            ]);
        } catch (Throwable $exception) {
            Log::error('Fout bij sp_Klant_Update.', [
                'klantId' => $klantId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
