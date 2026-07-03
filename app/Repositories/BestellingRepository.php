<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;

/**
 * Data-access laag voor bestellingen via MySQL stored procedures.
 */
class BestellingRepository
{
    /**
     * Haalt bestellingen op via sp_Bestelling_GetAll (INNER JOINs in stored procedure).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllBestellingen(?string $status = null): array
    {
        try {
            $resultaten = DB::select('CALL sp_Bestelling_GetAll(?)', [$status ?? '']);

            return array_map(static fn (object $rij): array => (array) $rij, $resultaten);
        } catch (PDOException $exception) {
            Log::error('Fout bij ophalen bestellingen via stored procedure.', [
                'status' => $status,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Haalt één bestelling op via sp_Bestelling_GetById.
     *
     * @return array<string, mixed>|null
     */
    public function getBestellingById(int $bestellingId): ?array
    {
        try {
            $resultaten = DB::select('CALL sp_Bestelling_GetById(?)', [$bestellingId]);
            $bestelling = $resultaten[0] ?? null;

            return $bestelling !== null ? (array) $bestelling : null;
        } catch (PDOException $exception) {
            Log::error('Fout bij ophalen bestelling op id.', [
                'bestellingId' => $bestellingId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Haalt productregels per bestelling op via sp_ProductPerBestelling_GetByBestellingId.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getProductenByBestellingId(int $bestellingId): array
    {
        try {
            $resultaten = DB::select('CALL sp_ProductPerBestelling_GetByBestellingId(?)', [$bestellingId]);

            return array_map(static fn (object $rij): array => (array) $rij, $resultaten);
        } catch (PDOException $exception) {
            Log::error('Fout bij ophalen producten per bestelling.', [
                'bestellingId' => $bestellingId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Haalt één productregel op via sp_ProductPerBestelling_GetById.
     *
     * @return array<string, mixed>|null
     */
    public function getProductPerBestellingById(int $productPerBestellingId): ?array
    {
        try {
            $resultaten = DB::select('CALL sp_ProductPerBestelling_GetById(?)', [$productPerBestellingId]);
            $productRegel = $resultaten[0] ?? null;

            return $productRegel !== null ? (array) $productRegel : null;
        } catch (PDOException $exception) {
            Log::error('Fout bij ophalen productregel op id.', [
                'productPerBestellingId' => $productPerBestellingId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Werkt het aantal van een productregel bij via sp_ProductPerBestelling_UpdateAantal.
     */
    public function updateProductAantal(int $productPerBestellingId, int $aantal): void
    {
        try {
            DB::select('CALL sp_ProductPerBestelling_UpdateAantal(?, ?)', [
                $productPerBestellingId,
                $aantal,
            ]);

            Log::info('Aantal productregel succesvol bijgewerkt.', [
                'productPerBestellingId' => $productPerBestellingId,
                'aantal' => $aantal,
            ]);
        } catch (PDOException $exception) {
            Log::error('Fout bij bijwerken aantal productregel.', [
                'productPerBestellingId' => $productPerBestellingId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
