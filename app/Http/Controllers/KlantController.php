<?php

namespace App\Http\Controllers;

use App\Http\Requests\KlantPostcodeSearchRequest;
use App\Http\Requests\UpdateKlantRequest;
use App\Repositories\KlantRepository;
use App\Services\KlantFormatter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PDOException;
use Throwable;

/**
 * Controller voor klant read- en update-functionaliteit (MVC).
 */
class KlantController extends Controller
{
    public function __construct(
        private readonly KlantRepository $klantRepository
    ) {}

    /**
     * Toont overzicht van alle klanten met optionele postcodefilter.
     */
    public function index(KlantPostcodeSearchRequest $request): View|RedirectResponse
    {
        try {
            $postcodeZoekterm = trim((string) $request->validated('postcode', ''));
            $huidigePagina = max(1, (int) $request->validated('page', 1));
            $itemsPerPagina = (int) config('kniploket.per_page', 4);

            $genormaliseerdePostcode = $postcodeZoekterm !== ''
                ? KlantFormatter::normalizePostcode($postcodeZoekterm)
                : null;

            $alleKlanten = $this->klantRepository->getAllKlanten($genormaliseerdePostcode);

            $klantenPaginator = new LengthAwarePaginator(
                collect($alleKlanten)->forPage($huidigePagina, $itemsPerPagina)->values(),
                count($alleKlanten),
                $itemsPerPagina,
                $huidigePagina,
                [
                    'path' => route('klanten.index'),
                    'query' => $request->query(),
                ]
            );

            return view('klanten.index', [
                'pageTitle' => 'Overzicht klanten - Kniploket Tiko',
                'activeNav' => 'klanten',
                'klanten' => $klantenPaginator,
                'postcode' => $postcodeZoekterm,
                'successMessage' => session('success'),
                'errorMessage' => session('error'),
                'autoHideFlash' => session()->has('success'),
                'flashAutoHideMs' => config('kniploket.flash_auto_hide_ms', 3000),
            ]);
        } catch (PDOException $exception) {
            Log::error('Databasefout in klantenoverzicht.', ['message' => $exception->getMessage()]);

            return redirect()
                ->route('home')
                ->with('error', 'Klantgegevens kunnen momenteel niet worden geladen.');
        }
    }

    /**
     * Toont detailpagina van één klant.
     */
    public function show(int $klant): View|RedirectResponse
    {
        try {
            $klantRecord = $this->klantRepository->getKlantById($klant);

            if ($klantRecord === null) {
                Log::warning('Klant niet gevonden.', ['klantId' => $klant]);

                return redirect()
                    ->route('klanten.index')
                    ->with('error', 'De geselecteerde klant bestaat niet.');
            }

            return view('klanten.show', [
                'pageTitle' => 'Klantdetail - Kniploket Tiko',
                'activeNav' => 'klanten',
                'klant' => $klantRecord,
            ]);
        } catch (PDOException $exception) {
            Log::error('Databasefout bij klantdetail.', ['message' => $exception->getMessage()]);

            return redirect()
                ->route('klanten.index')
                ->with('error', 'Klantgegevens kunnen niet worden geladen.');
        }
    }

    /**
     * Toont wijzigformulier voor een klant.
     */
    public function edit(int $klant): View|RedirectResponse
    {
        try {
            $klantRecord = $this->klantRepository->getKlantById($klant);

            if ($klantRecord === null) {
                return redirect()
                    ->route('klanten.index')
                    ->with('error', 'De geselecteerde klant bestaat niet.');
            }

            return view('klanten.edit', [
                'pageTitle' => 'Klant wijzigen - Kniploket Tiko',
                'activeNav' => 'klanten',
                'klant' => $klantRecord,
                'formData' => KlantFormatter::formFromRecord($klantRecord),
            ]);
        } catch (PDOException $exception) {
            Log::error('Databasefout bij klant wijzigen.', ['message' => $exception->getMessage()]);

            return redirect()
                ->route('klanten.index')
                ->with('error', 'Klantgegevens kunnen niet worden geladen.');
        }
    }

    /**
     * Verwerkt het wijzigformulier met validatie en beveiliging.
     */
    public function update(UpdateKlantRequest $request, int $klant): View|RedirectResponse
    {
        try {
            $klantRecord = $this->klantRepository->getKlantById($klant);

            if ($klantRecord === null) {
                return redirect()
                    ->route('klanten.index')
                    ->with('error', 'De geselecteerde klant bestaat niet.');
            }

            $naamDelen = KlantFormatter::parseNaam((string) $request->input('naam'));
            $gevalideerdeData = $request->validated();

            $this->klantRepository->updateKlant(
                (int) $klantRecord['Id'],
                (int) $klantRecord['ContactId'],
                [
                    'voornaam' => $naamDelen['voornaam'],
                    'tussenvoegsel' => $naamDelen['tussenvoegsel'],
                    'achternaam' => $naamDelen['achternaam'],
                    'bijzonderheden' => $gevalideerdeData['bijzonderheden'] ?? '',
                    'straatnaam' => $gevalideerdeData['straatnaam'],
                    'huisnummer' => $gevalideerdeData['huisnummer'],
                    'toevoeging' => $gevalideerdeData['toevoeging'] ?? '',
                    'postcode' => KlantFormatter::normalizePostcode($gevalideerdeData['postcode']),
                    'plaats' => $gevalideerdeData['plaats'],
                    'contact_email' => $gevalideerdeData['contact_email'],
                    'mobiel' => $gevalideerdeData['mobiel'],
                ]
            );

            return redirect()
                ->route('klanten.index')
                ->with('success', 'Klantgegevens bijgewerkt.');
        } catch (PDOException|Throwable $exception) {
            Log::error('Databasefout bij opslaan klant.', ['message' => $exception->getMessage()]);

            return redirect()
                ->route('klanten.index')
                ->with('error', 'Klantgegevens konden niet worden opgeslagen.');
        }
    }
}
