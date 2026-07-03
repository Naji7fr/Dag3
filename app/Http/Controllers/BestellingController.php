<?php

namespace App\Http\Controllers;

use App\Http\Requests\BestellingStatusFilterRequest;
use App\Repositories\BestellingRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PDOException;

/**
 * Controller voor bestelling read-functionaliteit (MVC).
 */
class BestellingController extends Controller
{
    public function __construct(
        private readonly BestellingRepository $bestellingRepository
    ) {}

    /**
     * Toont overzicht van alle bestellingen met optionele statusfilter.
     */
    public function index(BestellingStatusFilterRequest $request): View|RedirectResponse
    {
        try {
            $statusFilter = trim((string) $request->validated('status', ''));
            $huidigePagina = max(1, (int) $request->validated('page', 1));
            $itemsPerPagina = (int) config('kniploket.per_page', 4);

            $alleBestellingen = $this->bestellingRepository->getAllBestellingen(
                $statusFilter !== '' ? $statusFilter : null
            );

            $bestellingenPaginator = new LengthAwarePaginator(
                collect($alleBestellingen)->forPage($huidigePagina, $itemsPerPagina)->values(),
                count($alleBestellingen),
                $itemsPerPagina,
                $huidigePagina,
                [
                    'path' => route('bestellingen.index'),
                    'query' => $request->query(),
                ]
            );

            return view('bestellingen.index', [
                'pageTitle' => 'Overzicht bestellingen - Kniploket Tiko',
                'activeNav' => 'bestellingen',
                'bestellingen' => $bestellingenPaginator,
                'status' => $statusFilter,
                'statussen' => BestellingStatusFilterRequest::STATUSSEN,
            ]);
        } catch (PDOException $exception) {
            Log::error('Databasefout in bestellingenoverzicht.', ['message' => $exception->getMessage()]);

            return redirect()
                ->route('home')
                ->with('error', 'Bestellingen kunnen momenteel niet worden geladen.');
        }
    }
}
