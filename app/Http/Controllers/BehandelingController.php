<?php

/**
 * Behandeling Controller
 * 
 * Beheert alle behandelingen (treatments) en hun gerelateerde producten.
 * Dit controller zorgt voor:
 * - Weergave van alle behandelingen met filtering
 * - Weergave van producten per behandeling
 * - Bewerking van productprijzen
 * - Weergave van productdetails met leverancierinformatie
 */

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;
use Throwable;

/**
 * BehandelingController
 * 
 * Beheert behandelingen (treatments) en hun gerelateerde producten.
 * Alle database-fouten worden gelogd en gebruiker krijgt foutmelding.
 */
class BehandelingController extends Controller
{
    /**
     * Toont alle behandelingen met optionele filtering
     * 
     * GET /behandelingen
     * 
     * @param Request $request - HTTP request met optionele 'behandeling' query parameter
     * @return View|RedirectResponse - behandelingen.index view of redirect bij fout
     * 
     * Sorteervolgorde: 1. Combi > 2. Extensions > 3. Kleuren > 4. Knippen > 5. Overige
     */
    public function index(Request $request): View|RedirectResponse
    {
        try {
            $selectedBehandeling = $request->query('behandeling', null);
            
            // Haal ALLE actieve behandelingen op voor dropdown
            $allBehandelingen = DB::table('Behandeling')
                ->where('IsActief', true)
                ->orderBy('Naam')
                ->get();
            
            // Haal behandelingen op met aantal gerelateerde producten
            $query = DB::table('Behandeling as b')
                ->leftJoin('BehandelingPerVoorraad as bpv', 'b.Id', '=', 'bpv.BehandelingId')
                ->leftJoin('Voorraad as v', 'bpv.VoorraadId', '=', 'v.Id')
                ->leftJoin('Product as p', 'v.ProductId', '=', 'p.Id')
                ->where('b.IsActief', true)
                ->select(
                    'b.Id',
                    'b.Naam',
                    'b.Omschrijving',
                    'b.Duurminuten',
                    'b.Prijs',
                    DB::raw('COUNT(DISTINCT p.Id) as aantal_producten')
                )
                ->groupBy('b.Id', 'b.Naam', 'b.Omschrijving', 'b.Duurminuten', 'b.Prijs')
                ->orderByRaw("CASE 
                    WHEN b.Naam = 'Combi behandelingen' THEN 1
                    WHEN b.Naam = 'Extensions' THEN 2
                    WHEN b.Naam = 'Kleuren' THEN 3
                    WHEN b.Naam = 'Knippen' THEN 4
                    ELSE 5
                END");
            
            // Filter toepassen
            if ($selectedBehandeling && $selectedBehandeling !== 'Alle behandelingen') {
                $query->where('b.Naam', '=', $selectedBehandeling);
            }
            
            $behandelingen = $query->paginate(4);
            
            return view('behandelingen.index', [
                'behandelingen' => $behandelingen,
                'allBehandelingen' => $allBehandelingen,
                'selectedBehandeling' => $selectedBehandeling,
            ]);
        } catch (PDOException $exception) {
            Log::error('Databasefout bij ophalen behandelingen.', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('home')
                ->with('error', 'Behandelingen kunnen momenteel niet worden geladen.');
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in BehandelingController::index', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return redirect()
                ->route('home')
                ->with('error', 'Er is een onverwachte fout opgetreden.');
        }
    }

    /**
     * Toont alle producten voor een specifieke behandeling
     * 
     * GET /behandelingen/{id}
     * 
     * @param int $id - ID van de behandeling
     * @return View|RedirectResponse - behandelingen.show view of redirect bij fout
     */
    public function show(int $id): View|RedirectResponse
    {
        try {
            // Haal behandeling op
            $behandeling = DB::table('Behandeling')
                ->where('Id', $id)
                ->where('IsActief', true)
                ->firstOrFail();
            
            // Haal alle producten voor deze behandeling
            $producten = DB::table('BehandelingPerVoorraad as bpv')
                ->join('Voorraad as v', 'bpv.VoorraadId', '=', 'v.Id')
                ->join('Product as p', 'v.ProductId', '=', 'p.Id')
                ->where('bpv.BehandelingId', $id)
                ->where('bpv.IsActief', true)
                ->select('p.*', 'v.AantalOpVoorraad')
                ->get();
            
            return view('behandelingen.show', [
                'behandeling' => $behandeling,
                'producten' => $producten,
            ]);
        } catch (PDOException $exception) {
            Log::error('Databasefout bij ophalen behandelingdetails.', [
                'behandelingId' => $id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('behandelingen.index')
                ->with('error', 'Behandelinggegevens kunnen niet worden geladen.');
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in BehandelingController::show', [
                'behandelingId' => $id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('behandelingen.index')
                ->with('error', 'Er is een onverwachte fout opgetreden.');
        }
    }

    /**
     * Toont bewerk-formulier voor productprijs
     * 
     * GET /behandelingen/product/{productId}/wijzigen
     * 
     * @param int $productId - ID van het product
     * @return View|RedirectResponse - behandelingen.edit-product view of redirect bij fout
     */
    public function editProduct(int $productId): View|RedirectResponse
    {
        try {
            // Haal product op
            $product = DB::table('Product')
                ->where('Id', $productId)
                ->where('IsActief', true)
                ->firstOrFail();
            
            // Haal leverancier informatie op
            $leverancier = DB::table('LeverancierOrder as lo')
                ->join('Leverancier as l', 'lo.LeverancierId', '=', 'l.Id')
                ->where('lo.ProductId', $productId)
                ->select('l.*')
                ->first();
            
            return view('behandelingen.edit-product', [
                'product' => $product,
                'leverancier' => $leverancier,
            ]);
        } catch (PDOException $exception) {
            Log::error('Databasefout bij ophalen productdetails.', [
                'productId' => $productId,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('behandelingen.index')
                ->with('error', 'Productgegevens kunnen niet worden geladen.');
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in BehandelingController::editProduct', [
                'productId' => $productId,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('behandelingen.index')
                ->with('error', 'Er is een onverwachte fout opgetreden.');
        }
    }

    /**
     * Update productprijs met validatie
     * 
     * PUT /behandelingen/product/{productId}
     * 
     * @param Request $request - HTTP request met 'verkoopprijs' input
     * @param int $productId - ID van het product
     * @return RedirectResponse - Redirect naar product detail pagina
     * 
     * Validatie: verkoopprijs minimaal 30% boven inkoopprijs (business rule)
     */
    public function updateProduct(Request $request, int $productId): RedirectResponse
    {
        try {
            // Haal product op met inkoopprijs (voor validatie)
            $product = DB::table('Product')
                ->where('Id', $productId)
                ->where('IsActief', true)
                ->firstOrFail();
            
            // Bereken minimumverkoopprijs: inkoopprijs × 1,30
            $minVerkoopprijs = $product->InkoopPrijs * 1.30;
            
            // Valideer invoer
            $validated = $request->validate([
                'verkoopprijs' => [
                    'required',
                    'numeric',
                    'min:' . $minVerkoopprijs,
                ],
            ], [
                'verkoopprijs.required' => 'Verkoopprijs is verplicht.',
                'verkoopprijs.numeric' => 'Verkoopprijs moet een getal zijn.',
                'verkoopprijs.min' => 'Verkoopprijs moet minimaal 30 procent boven de inkoopprijs liggen.',
            ]);
            
            // Update productprijs
            DB::table('Product')
                ->where('Id', $productId)
                ->update([
                    'VerkoopPrijs' => $validated['verkoopprijs'],
                    'DatumGewijzigd' => now(),
                ]);
            
            Log::info('Productprijs bijgewerkt.', [
                'productId' => $productId,
                'newPrice' => $validated['verkoopprijs'],
            ]);
            
            return redirect()
                ->route('behandelingen.product-detail', $productId)
                ->with('success', 'Productprijs bijgewerkt');
        } catch (PDOException $exception) {
            Log::error('Databasefout bij bijwerken productprijs.', [
                'productId' => $productId,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('behandelingen.index')
                ->with('error', 'Productprijs kon niet worden opgeslagen.');
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in BehandelingController::updateProduct', [
                'productId' => $productId,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('behandelingen.index')
                ->with('error', 'Er is een onverwachte fout opgetreden.');
        }
    }


    /**
     * Toont productdetails met leverancierinformatie
     * 
     * GET /behandelingen/product/{productId}
     * 
     * @param int $productId - ID van het product
     * @return View|RedirectResponse - behandelingen.product-detail view of redirect bij fout
     */
    public function showProduct(int $productId): View|RedirectResponse
    {
        try {
            // Haal product op met categorie informatie
            $product = DB::table('Product as p')
                ->leftJoin('Categorie as c', 'p.CategorieId', '=', 'c.Id')
                ->where('p.Id', $productId)
                ->where('p.IsActief', true)
                ->select('p.*', 'c.Naam as CategoriaNaam')
                ->firstOrFail();
            
            // Haal leverancier informatie op
            $leverancier = DB::table('LeverancierOrder as lo')
                ->join('Leverancier as l', 'lo.LeverancierId', '=', 'l.Id')
                ->where('lo.ProductId', $productId)
                ->select('l.*')
                ->first();
            
            return view('behandelingen.product-detail', [
                'product' => $product,
                'leverancier' => $leverancier,
            ]);
        } catch (PDOException $exception) {
            Log::error('Databasefout bij ophalen productdetails.', [
                'productId' => $productId,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('behandelingen.index')
                ->with('error', 'Productgegevens kunnen niet worden geladen.');
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in BehandelingController::showProduct', [
                'productId' => $productId,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('behandelingen.index')
                ->with('error', 'Er is een onverwachte fout opgetreden.');
        }
    }
}
