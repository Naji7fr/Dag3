<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;

/**
 * Data-access laag voor medewerkers via MySQL stored procedures.
 * Implementeert repository pattern voor scheiding van concerns.
 */
class MedewerkerRepository
{
    /**
     * Haalt medewerkers op via sp_Medewerker_GetAll (INNER JOINs in stored procedure).
     * Optionele filter op specialisatie.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllMedewerkers(?string $specialisatie = null): array
    {
        try {
            $resultaten = DB::select(
                'CALL sp_Medewerker_GetAll(?)',
                [$specialisatie ?? '']
            );

            return array_map(static fn (object $rij): array => (array) $rij, $resultaten);
        } catch (PDOException $exception) {
            Log::error('Fout bij ophalen medewerkers via stored procedure.', [
                'specialisatie' => $specialisatie,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Haalt één medewerker op via sp_Medewerker_GetById.
     *
     * @return array<string, mixed>|null
     */
    public function getMedewerkerById(int $medewerkerId): ?array
    {
        try {
            $resultaten = DB::select('CALL sp_Medewerker_GetById(?)', [$medewerkerId]);
            $medewerker = $resultaten[0] ?? null;

            return $medewerker !== null ? (array) $medewerker : null;
        } catch (PDOException $exception) {
            Log::error('Fout bij ophalen medewerker op id.', [
                'medewerkerId' => $medewerkerId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Haalt alle unieke specialisaties op van actieve medewerkers.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSpecialisaties(): array
    {
        try {
            $resultaten = DB::select('CALL sp_Medewerker_GetSpecialisaties()');

            return array_map(static fn (object $rij): array => (array) $rij, $resultaten);
        } catch (PDOException $exception) {
            Log::error('Fout bij ophalen specialisaties via stored procedure.', [
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Werkt een medewerker bij via sp_Medewerker_Update.
     *
     * @return void
     */
    public function updateMedewerker(
        int $medewerkerId,
        int $contactId,
        string $voornaam,
        ?string $tussenvoegsel,
        string $achternaam,
        string $specialisatie,
        string $geboortedatum,
        string $straatnaam,
        string $huisnummer,
        ?string $toevoeging,
        string $postcode,
        string $plaats,
        string $contactEmail,
        string $mobiel
    ): void {
        try {
            DB::statement(
                'CALL sp_Medewerker_Update(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $medewerkerId,
                    $contactId,
                    $voornaam,
                    $tussenvoegsel,
                    $achternaam,
                    $specialisatie,
                    $geboortedatum,
                    $straatnaam,
                    $huisnummer,
                    $toevoeging,
                    $postcode,
                    $plaats,
                    $contactEmail,
                    $mobiel,
                ]
            );
        } catch (PDOException $exception) {
            Log::error('Fout bij bijwerken medewerker via stored procedure.', [
                'medewerkerId' => $medewerkerId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
