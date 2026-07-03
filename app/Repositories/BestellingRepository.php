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
}
