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

class BehandelingController extends Controller
{
    /**
     * Toont alle behandelingen met optionele filtering
     * 
     * GET /behandelingen
     * 
     * @param Request $request - HTTP request met optionele 'behandeling' query parameter
     * @return View - behandelingen.index view met:
     *               - behandelingen: lijst met paginatie (4 per pagina)
     *               - allBehandelingen: alle beschikbare behandelingen voor dropdown
     *               - selectedBehandeling: geselecteerde filter (null als geen)
     * 
     * Sorteervolgorde (vast):
     * 1. Combi behandelingen
     * 2. Extensions
     * 3. Kleuren
     * 4. Knippen
     * 5. Overige
     */
    public function index(Request $request): View
    {
        // Haal de geselecteerde behandeling uit de query string
        // Dit wordt gebruikt voor filtering in de dropdown
        $selectedBehandeling = $request->query('behandeling', null);
        
        // Query 1: Haal ALLE actieve behandelingen op voor de filter dropdown
        // Deze zijn alfabetisch gesorteerd voor gemakkelijk zoeken
        $allBehandelingen = DB::table('Behandeling')
            ->where('IsActief', true)
            ->orderBy('Naam')
            ->get();
        
        // Query 2: Haal behandelingen op MET het aantal gerelateerde producten
        // Relaties: Behandeling -> BehandelingPerVoorraad -> Voorraad -> Product
        // We gebruiken leftJoin omdat een behandeling ook 0 producten kan hebben
        $query = DB::table('Behandeling as b')
            ->leftJoin('BehandelingPerVoorraad as bpv', 'b.Id', '=', 'bpv.BehandelingId')
            ->leftJoin('Voorraad as v', 'bpv.VoorraadId', '=', 'v.Id')
            ->leftJoin('Product as p', 'v.ProductId', '=', 'p.Id')
            ->where('b.IsActief', true)  // Alleen actieve behandelingen
            ->select(
                'b.Id',
                'b.Naam',
                'b.Omschrijving',
                'b.Duurminuten',
                'b.Prijs',
                DB::raw('COUNT(DISTINCT p.Id) as aantal_producten')  // Tel unieke producten
            )
            ->groupBy('b.Id', 'b.Naam', 'b.Omschrijving', 'b.Duurminuten', 'b.Prijs')
            // Vaste sorteervolgorde per business requirement
            ->orderByRaw("CASE 
                WHEN b.Naam = 'Combi behandelingen' THEN 1
                WHEN b.Naam = 'Extensions' THEN 2
                WHEN b.Naam = 'Kleuren' THEN 3
                WHEN b.Naam = 'Knippen' THEN 4
                ELSE 5
            END");
        
        // Filter toepassen als gebruiker iets heeft geselecteerd
        if ($selectedBehandeling && $selectedBehandeling !== 'Alle behandelingen') {
            $query->where('b.Naam', '=', $selectedBehandeling);
        }
        
        // Paginatie: 4 items per pagina
        $behandelingen = $query->paginate(4);
        
        // Render de view met alle gegevens
        return view('behandelingen.index', [
            'behandelingen' => $behandelingen,           // Gefilterde behandelingen met paginatie
            'allBehandelingen' => $allBehandelingen,     // Alle behandelingen voor dropdown
            'selectedBehandeling' => $selectedBehandeling, // Huidige filter
        ]);
    }

    /**
     * Toont alle producten voor een specifieke behandeling
     * 
     * GET /behandelingen/{id}
     * 
     * @param int $id - ID van de behandeling
     * @return View - behandelingen.show view met:
     *               - behandeling: behandeling details
     *               - producten: lijst met producten en voorraadaantallen
     * 
     * Deze methode haalt de behandeling op en alle gerelateerde producten
     * via de BehandelingPerVoorraad linking table.
     */
    public function show(int $id): View
    {
        // Haal behandeling op met gegeven ID
        // firstOrFail() geeft 404 als niet gevonden
        $behandeling = DB::table('Behandeling')
            ->where('Id', $id)
            ->where('IsActief', true)  // Alleen actieve behandelingen
            ->firstOrFail();
        
        // Haal alle producten voor deze behandeling
        // Relationeel pad: BehandelingPerVoorraad (linking) -> Voorraad -> Product
        $producten = DB::table('BehandelingPerVoorraad as bpv')
            ->join('Voorraad as v', 'bpv.VoorraadId', '=', 'v.Id')
            ->join('Product as p', 'v.ProductId', '=', 'p.Id')
            ->where('bpv.BehandelingId', $id)  // Producten voor deze behandeling
            ->where('bpv.IsActief', true)      // Alleen actieve koppelingen
            ->select('p.*', 'v.AantalOpVoorraad')  // Include voorraadaantal
            ->get();
        
        return view('behandelingen.show', [
            'behandeling' => $behandeling,
            'producten' => $producten,
        ]);
    }

    /**
     * Toont bewerk-formulier voor productprijs
     * 
     * GET /behandelingen/product/{productId}/wijzigen
     * 
     * @param int $productId - ID van het product
     * @return View - behandelingen.edit-product view met:
     *               - product: product details
     *               - leverancier: leverancier informatie (als beschikbaar)
     * 
     * Deze methode haalt het product en de leverancier informatie op
     * zodat de gebruiker de prijs kan wijzigen.
     */
    public function editProduct(int $productId): View
    {
        // Haal product op
        $product = DB::table('Product as p')
            ->leftJoin('Leverancier as l', function ($join) {
                $join->on('p.Id', '=', DB::raw('(SELECT ProductId FROM LeverancierOrder WHERE ProductId = p.Id LIMIT 1)'));
            })
            ->select('p.*')  // Alle product kolommen
            ->where('p.Id', $productId)
            ->where('p.IsActief', true)  // Alleen actieve producten
            ->firstOrFail();  // 404 als niet gevonden
        
        // Haal leverancier informatie op via LeverancierOrder
        // first() retourneert null als geen leverancier gevonden
        $leverancier = DB::table('LeverancierOrder as lo')
            ->join('Leverancier as l', 'lo.LeverancierId', '=', 'l.Id')
            ->where('lo.ProductId', $productId)  // Leverancierorder voor dit product
            ->select('l.*')  // Alle leverancier gegevens
            ->first();  // Haal eerste (meestal enige) leverancier
        
        return view('behandelingen.edit-product', [
            'product' => $product,
            'leverancier' => $leverancier,
        ]);
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
     * Validatie:
     * - Verkoopprijs moet numeriek zijn
     * - Verkoopprijs moet minimaal 30% boven inkoopprijs liggen (business rule)
     * 
     * Bij succes: product bijgewerkt en succes-bericht getoond
     * Bij fout: terug naar formulier met fouten
     */
    public function updateProduct(Request $request, int $productId): RedirectResponse
    {
        // Haal product op met inkoopprijs
        // Deze hebben we nodig om minimumprijs te berekenen
        $product = DB::table('Product')
            ->where('Id', $productId)
            ->where('IsActief', true)  // Alleen actieve producten
            ->firstOrFail();
        
        // Bereken minimumverkoopprijs: inkoopprijs × 1,30 (30% markup)
        // Dit is een business rule voor winstmarge
        $minVerkoopprijs = $product->InkoopPrijs * 1.30;
        
        // Valideer invoer
        $validated = $request->validate([
            'verkoopprijs' => [
                'required',          // Verplicht
                'numeric',           // Moet getal zijn
                'min:' . $minVerkoopprijs,  // Minimaal 30% boven inkoopprijs
            ],
        ], [
            'verkoopprijs.min' => 'Verkoopprijs moet minimaal 30 procent boven de inkoopprijs liggen.',
        ]);
        
        // Update productprijs in database
        DB::table('Product')
            ->where('Id', $productId)
            ->update([
                'VerkoopPrijs' => $validated['verkoopprijs'],  // Nieuwe prijs
                'DatumGewijzigd' => now(),  // Timestamp van wijziging
            ]);
        
        // Redirect terug naar product detail met succes-bericht
        return redirect()
            ->route('behandelingen.product-detail', $productId)
            ->with('success', 'Productprijs bijgewerkt');
    }


    /**
     * Toont productdetails met leverancierinformatie
     * 
     * GET /behandelingen/product/{productId}
     * 
     * @param int $productId - ID van het product
     * @return View - behandelingen.product-detail view met:
     *               - product: volledige product informatie
     *               - leverancier: leverancier details (als beschikbaar)
     * 
     * Deze methode wordt gebruikt in twee contexten:
     * 1. Na het bekijken van een product uit behandeling
     * 2. Na het wijzigen van de prijs (terug naar detail)
     * 
     * Toont alle product info inclusief leverancier contact gegevens.
     */
    public function showProduct(int $productId): View
    {
        // Haal product op met categorie informatie
        $product = DB::table('Product as p')
            ->leftJoin('Categorie as c', 'p.CategorieId', '=', 'c.Id')
            ->where('p.Id', $productId)
            ->where('p.IsActief', true)  // Alleen actieve producten
            ->select('p.*', 'c.Naam as CategoriaNaam')  // Voeg categorienaam toe
            ->firstOrFail();  // 404 als niet gevonden
        
        // Haal leverancier informatie op
        // LeverancierOrder bevat de koppelingen tussen Product en Leverancier
        $leverancier = DB::table('LeverancierOrder as lo')
            ->join('Leverancier as l', 'lo.LeverancierId', '=', 'l.Id')
            ->where('lo.ProductId', $productId)  // Leverancierorder voor dit product
            ->select('l.*')  // Alle leverancier details
            ->first();  // Haal eerste leverancier (null als geen)
        
        return view('behandelingen.product-detail', [
            'product' => $product,      // Product gegevens
            'leverancier' => $leverancier,  // Leverancier gegevens (kan null zijn)
        ]);
    }
}
