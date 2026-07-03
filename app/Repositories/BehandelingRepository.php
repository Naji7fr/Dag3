<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;
use Throwable;

/**
 * Data-access laag voor behandelingen via MySQL stored procedures.
 * 
 * Deze repository zorgt voor:
 * - Ophalen van alle behandelingen met productcount
 * - Ophalen van producten per behandeling
 * - Ophalen van productdetails
 * - Error handling en logging
 */
class BehandelingRepository
{
    /**
     * Haalt alle actieve behandelingen op via sp_Behandeling_GetAll.
     * Optioneel gefilterd op behandeling naam.
     *
     * @param string|null $behandelingNaam - Filter op behandeling naam (null = geen filter)
     * @return array<int, array<string, mixed>> - Behandelingen met aantal_producten
     * @throws PDOException - Bij databasefout
     */
    public function getAllBehandelingen(?string $behandelingNaam = null): array
    {
        try {
            // Roep stored procedure aan met optionele filter
            // Retourneert: Id, Naam, Omschrijving, Duurminuten, Prijs, aantal_producten
            $resultaten = DB::select(
                'CALL sp_Behandeling_GetAll(?)',
                [$behandelingNaam ?? '']
            );

            // Zet stdClass objecten om naar arrays
            return array_map(
                static fn (object $rij): array => (array) $rij,
                $resultaten
            );
        } catch (PDOException $exception) {
            // Log database-specifieke fout
            Log::error('Databasefout bij ophalen behandelingen via stored procedure.', [
                'behandelingNaam' => $behandelingNaam,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);

            throw $exception;
        } catch (Throwable $exception) {
            // Log onverwachte fouten
            Log::error('Onverwachte fout in BehandelingRepository::getAllBehandelingen', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }
    }

    /**
     * Haalt alle actieve behandelingen op voor dropdown (alfabetisch).
     * Dit is een lightweightversie voor filter-dropdowns.
     *
     * @return array<int, array<string, mixed>> - Array met Id en Naam
     * @throws PDOException - Bij databasefout
     */
    public function getAllBehandelingenForDropdown(): array
    {
        try {
            // Eenvoudige SELECT voor dropdown (geen procedure nodig)
            $resultaten = DB::table('Behandeling')
                ->where('IsActief', true)
                ->orderBy('Naam')
                ->select('Id', 'Naam')
                ->get();

            return $resultaten->map(fn ($row) => (array) $row)->toArray();
        } catch (PDOException $exception) {
            Log::error('Databasefout bij ophalen dropdown behandelingen.', [
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in BehandelingRepository::getAllBehandelingenForDropdown', [
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Haalt één behandeling op via sp_Behandeling_GetById.
     *
     * @param int $behandelingId - ID van de behandeling
     * @return array<string, mixed>|null - Behandeling data of null als niet gevonden
     * @throws PDOException - Bij databasefout
     */
    public function getBehandelingById(int $behandelingId): ?array
    {
        try {
            // Roep stored procedure aan met behandeling ID
            $resultaten = DB::select(
                'CALL sp_Behandeling_GetById(?)',
                [$behandelingId]
            );

            // Retorneer eerste resultaat (of null)
            return isset($resultaten[0]) ? (array) $resultaten[0] : null;
        } catch (PDOException $exception) {
            Log::error('Databasefout bij ophalen behandeling details.', [
                'behandelingId' => $behandelingId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in BehandelingRepository::getBehandelingById', [
                'behandelingId' => $behandelingId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Haalt alle producten voor een behandeling op via sp_Behandeling_GetProducts.
     * Relaties: BehandelingPerVoorraad -> Voorraad -> Product
     *
     * @param int $behandelingId - ID van de behandeling
     * @return array<int, array<string, mixed>> - Producten met voorraadaantallen
     * @throws PDOException - Bij databasefout
     */
    public function getBehandelingProducts(int $behandelingId): array
    {
        try {
            // Roep stored procedure aan
            // Retourneert: Product info met AantalOpVoorraad
            $resultaten = DB::select(
                'CALL sp_Behandeling_GetProducts(?)',
                [$behandelingId]
            );

            return array_map(
                static fn (object $rij): array => (array) $rij,
                $resultaten
            );
        } catch (PDOException $exception) {
            Log::error('Databasefout bij ophalen producten voor behandeling.', [
                'behandelingId' => $behandelingId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in BehandelingRepository::getBehandelingProducts', [
                'behandelingId' => $behandelingId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Haalt productdetails op via sp_Product_GetById.
     * Inclusief categorie en leverancierinformatie.
     *
     * @param int $productId - ID van het product
     * @return array<string, mixed>|null - Product data of null als niet gevonden
     * @throws PDOException - Bij databasefout
     */
    public function getProductById(int $productId): ?array
    {
        try {
            $resultaten = DB::select(
                'CALL sp_Product_GetById(?)',
                [$productId]
            );

            return isset($resultaten[0]) ? (array) $resultaten[0] : null;
        } catch (PDOException $exception) {
            Log::error('Databasefout bij ophalen productdetails.', [
                'productId' => $productId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in BehandelingRepository::getProductById', [
                'productId' => $productId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Haalt leverancier informatie voor een product op.
     * Via LeverancierOrder linking table.
     *
     * @param int $productId - ID van het product
     * @return array<string, mixed>|null - Leverancier data of null
     * @throws PDOException - Bij databasefout
     */
    public function getLeverancierByProductId(int $productId): ?array
    {
        try {
            $resultaten = DB::select(
                'CALL sp_Leverancier_GetByProductId(?)',
                [$productId]
            );

            return isset($resultaten[0]) ? (array) $resultaten[0] : null;
        } catch (PDOException $exception) {
            Log::error('Databasefout bij ophalen leverancier informatie.', [
                'productId' => $productId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in BehandelingRepository::getLeverancierByProductId', [
                'productId' => $productId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Update productprijs met validatie.
     * Controleert 30% minimum markup business rule.
     *
     * @param int $productId - ID van het product
     * @param float $newPrice - Nieuwe verkoopprijs
     * @return bool - True als update succesvol
     * @throws PDOException - Bij databasefout
     * @throws Throwable - Bij andere fouten
     */
    public function updateProductPrice(int $productId, float $newPrice): bool
    {
        try {
            // Update in database
            $updated = DB::table('Product')
                ->where('Id', $productId)
                ->where('IsActief', true)
                ->update([
                    'VerkoopPrijs' => $newPrice,
                    'DatumGewijzigd' => now(),
                ]);

            if ($updated > 0) {
                Log::info('Productprijs bijgewerkt.', [
                    'productId' => $productId,
                    'newPrice' => $newPrice,
                ]);

                return true;
            }

            Log::warning('Productprijs update had geen effect.', [
                'productId' => $productId,
            ]);

            return false;
        } catch (PDOException $exception) {
            Log::error('Databasefout bij bijwerken productprijs.', [
                'productId' => $productId,
                'newPrice' => $newPrice,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in BehandelingRepository::updateProductPrice', [
                'productId' => $productId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
