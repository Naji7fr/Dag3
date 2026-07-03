<?php

namespace App\Http\Controllers;

use App\Http\Requests\BestellingStatusFilterRequest;
use App\Http\Requests\UpdateProductPerBestellingRequest;
use App\Repositories\BestellingRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PDOException;
use Throwable;

/**
 * Controller voor bestelling read- en update-functionaliteit (MVC).
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

    /**
     * Toont producten per bestelling.
     */
    public function producten(int $bestelling): View|RedirectResponse
    {
        try {
            $bestellingRecord = $this->bestellingRepository->getBestellingById($bestelling);

            if ($bestellingRecord === null) {
                return redirect()
                    ->route('bestellingen.index')
                    ->with('error', 'De geselecteerde bestelling bestaat niet.');
            }

            $productRegels = $this->bestellingRepository->getProductenByBestellingId($bestelling);

            return view('bestellingen.producten.index', [
                'pageTitle' => 'Producten per bestelling - Kniploket Tiko',
                'activeNav' => 'bestellingen',
                'bestelling' => $bestellingRecord,
                'productRegels' => $productRegels,
                'successMessage' => session('success'),
                'errorMessage' => session('error'),
                'autoHideFlash' => session()->has('success'),
                'flashAutoHideMs' => config('kniploket.flash_auto_hide_ms', 3000),
            ]);
        } catch (PDOException $exception) {
            Log::error('Databasefout bij producten per bestelling.', ['message' => $exception->getMessage()]);

            return redirect()
                ->route('bestellingen.index')
                ->with('error', 'Producten kunnen niet worden geladen.');
        }
    }

    /**
     * Toont wijzigformulier voor een bestelproduct.
     */
    public function editProduct(int $bestelling, int $productPerBestelling): View|RedirectResponse
    {
        try {
            $bestellingRecord = $this->bestellingRepository->getBestellingById($bestelling);

            if ($bestellingRecord === null) {
                return redirect()
                    ->route('bestellingen.index')
                    ->with('error', 'De geselecteerde bestelling bestaat niet.');
            }

            $productRegel = $this->bestellingRepository->getProductPerBestellingById($productPerBestelling);

            if ($productRegel === null || (int) $productRegel['BestellingId'] !== $bestelling) {
                return redirect()
                    ->route('bestellingen.producten', $bestelling)
                    ->with('error', 'Het geselecteerde product bestaat niet in deze bestelling.');
            }

            return view('bestellingen.producten.edit', [
                'pageTitle' => 'Bestelproduct wijzigen - Kniploket Tiko',
                'activeNav' => 'bestellingen',
                'bestelling' => $bestellingRecord,
                'productRegel' => $productRegel,
            ]);
        } catch (PDOException $exception) {
            Log::error('Databasefout bij bestelproduct wijzigen.', ['message' => $exception->getMessage()]);

            return redirect()
                ->route('bestellingen.producten', $bestelling)
                ->with('error', 'Bestelproduct kan niet worden geladen.');
        }
    }

    /**
     * Verwerkt het wijzigformulier voor een bestelproduct.
     */
    public function updateProduct(
        UpdateProductPerBestellingRequest $request,
        int $bestelling,
        int $productPerBestelling
    ): View|RedirectResponse {
        try {
            $bestellingRecord = $this->bestellingRepository->getBestellingById($bestelling);

            if ($bestellingRecord === null) {
                return redirect()
                    ->route('bestellingen.index')
                    ->with('error', 'De geselecteerde bestelling bestaat niet.');
            }

            $productRegel = $this->bestellingRepository->getProductPerBestellingById($productPerBestelling);

            if ($productRegel === null || (int) $productRegel['BestellingId'] !== $bestelling) {
                return redirect()
                    ->route('bestellingen.producten', $bestelling)
                    ->with('error', 'Het geselecteerde product bestaat niet in deze bestelling.');
            }

            $this->bestellingRepository->updateProductAantal(
                $productPerBestelling,
                (int) $request->validated('aantal')
            );

            return redirect()
                ->route('bestellingen.producten', $bestelling)
                ->with('success', 'Aantal producten bijgewerkt');
        } catch (PDOException|Throwable $exception) {
            Log::error('Databasefout bij opslaan bestelproduct.', ['message' => $exception->getMessage()]);

            return redirect()
                ->route('bestellingen.producten', $bestelling)
                ->with('error', 'Aantal producten kon niet worden bijgewerkt.');
        }
    }
}
