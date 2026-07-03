<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedewerkerFilterRequest;
use App\Http\Requests\UpdateMedewerkerRequest;
use App\Repositories\MedewerkerRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PDOException;
use Throwable;

/**
 * Controller voor medewerker read-functionaliteit (MVC).
 * Beheert het overzicht en detailpagina van medewerkers.
 */
class MedewerkerController extends Controller
{
    public function __construct(
        private readonly MedewerkerRepository $medewerkerRepository
    ) {}

    /**
     * Toont overzicht van alle medewerkers met optionele specialisatiefilter.
     * Implementeert pagination, filtering en error handling.
     *
     * @param MedewerkerFilterRequest $request Gevalideerde filterparameters
     * @return View|RedirectResponse
     */
    public function index(MedewerkerFilterRequest $request): View|RedirectResponse
    {
        try {
            // Haal valideerde parameters op met trimmen en normalisering
            $specialisatieFilter = trim((string) $request->validated('specialisatie', ''));
            $huidigePagina = max(1, (int) $request->validated('page', 1));
            $itemsPerPagina = (int) config('kniploket.per_page', 10);

            // Zet filter naar null als leeg, anders gebruik de filter
            $genormaliseerdeSpecialisatie = $specialisatieFilter !== '' ? $specialisatieFilter : null;

            // Haal medewerkers op via repository
            $alleMedewerkers = $this->medewerkerRepository->getAllMedewerkers($genormaliseerdeSpecialisatie);

            // Haal beschikbare specialisaties op voor dropdown
            $specialisaties = $this->medewerkerRepository->getSpecialisaties();

            // Implementeer pagination handmatig voor aangepaste data
            $medewerkersPaginator = new LengthAwarePaginator(
                collect($alleMedewerkers)->forPage($huidigePagina, $itemsPerPagina)->values(),
                count($alleMedewerkers),
                $itemsPerPagina,
                $huidigePagina,
                [
                    'path' => route('medewerkers.index'),
                    'query' => $request->query(),
                ]
            );

            // Log succesvolle request
            Log::info('Medewerkers overzicht opgehaald.', [
                'specialisatie_filter' => $genormaliseerdeSpecialisatie,
                'aantal_resultaten' => count($alleMedewerkers),
                'pagina' => $huidigePagina,
            ]);

            return view('medewerkers.index', [
                'pageTitle' => 'Overzicht medewerkers - Kniploket Tiko',
                'activeNav' => 'medewerkers',
                'medewerkers' => $medewerkersPaginator,
                'specialisaties' => $specialisaties,
                'specialisatie' => $specialisatieFilter,
                'successMessage' => session('success'),
                'errorMessage' => session('error'),
                'autoHideFlash' => session()->has('success'),
                'flashAutoHideMs' => config('kniploket.flash_auto_hide_ms', 3000),
            ]);
        } catch (PDOException $exception) {
            // Log databasefouten
            Log::error('Databasefout bij ophalen medewerkers.', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Er is een fout opgetreden bij het ophalen van medewerkers. Probeer het later opnieuw.');
        } catch (Throwable $exception) {
            // Log onverwachte fouten
            Log::error('Onverwachte fout in MedewerkerController::index', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Er is een onverwachte fout opgetreden. Probeer het later opnieuw.');
        }
    }

    /**
     * Toont detailpagina van één medewerker.
     * Implementeert error handling en logging.
     *
     * @param int $id Medewerker ID
     * @return View|RedirectResponse
     */
    public function show(int $id): View|RedirectResponse
    {
        try {
            // Haal medewerker op via repository
            $medewerker = $this->medewerkerRepository->getMedewerkerById($id);

            if ($medewerker === null) {
                Log::warning('Medewerker niet gevonden.', [
                    'medewerkerId' => $id,
                ]);

                return redirect()->route('medewerkers.index')
                    ->with('error', 'Medewerker niet gevonden.');
            }

            // Log succesvolle request
            Log::info('Medewerker detailpagina opgehaald.', [
                'medewerkerId' => $id,
            ]);

            return view('medewerkers.show', [
                'pageTitle' => 'Details ' . $medewerker['Voornaam'] . ' ' . $medewerker['Achternaam'] . ' - Kniploket Tiko',
                'activeNav' => 'medewerkers',
                'medewerker' => $medewerker,
            ]);
        } catch (PDOException $exception) {
            // Log databasefouten
            Log::error('Databasefout bij ophalen medewerker details.', [
                'medewerkerId' => $id,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);

            return redirect()->route('medewerkers.index')
                ->with('error', 'Er is een fout opgetreden bij het ophalen van medewerkergegevens.');
        } catch (Throwable $exception) {
            // Log onverwachte fouten
            Log::error('Onverwachte fout in MedewerkerController::show', [
                'medewerkerId' => $id,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return redirect()->route('medewerkers.index')
                ->with('error', 'Er is een onverwachte fout opgetreden.');
        }
    }

    /**
     * Toont edit-formulier voor een medewerker.
     *
     * @param int $id Medewerker ID
     * @return View|RedirectResponse
     */
    public function edit(int $id): View|RedirectResponse
    {
        try {
            $medewerker = $this->medewerkerRepository->getMedewerkerById($id);

            if ($medewerker === null) {
                Log::warning('Medewerker niet gevonden voor edit.', [
                    'medewerkerId' => $id,
                ]);

                return redirect()->route('medewerkers.index')
                    ->with('error', 'Medewerker niet gevonden.');
            }

            // Haal specialisaties op voor dropdown
            $specialisaties = $this->medewerkerRepository->getSpecialisaties();

            Log::info('Medewerker editpagina opgehaald.', [
                'medewerkerId' => $id,
            ]);

            return view('medewerkers.edit', [
                'pageTitle' => 'Medewerker wijzigen ' . $medewerker['Voornaam'] . ' ' . $medewerker['Achternaam'] . ' - Kniploket Tiko',
                'activeNav' => 'medewerkers',
                'medewerker' => $medewerker,
                'specialisaties' => $specialisaties,
            ]);
        } catch (PDOException $exception) {
            Log::error('Databasefout bij ophalen medewerker voor edit.', [
                'medewerkerId' => $id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('medewerkers.index')
                ->with('error', 'Er is een fout opgetreden bij het ophalen van medewerkergegevens.');
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in MedewerkerController::edit', [
                'medewerkerId' => $id,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return redirect()->route('medewerkers.index')
                ->with('error', 'Er is een onverwachte fout opgetreden.');
        }
    }

    /**
     * Werkt medewerker-gegevens bij.
     * Implementeert error handling en logging.
     *
     * @param UpdateMedewerkerRequest $request Gevalideerde update data
     * @param int $id Medewerker ID
     * @return RedirectResponse
     */
    public function update(UpdateMedewerkerRequest $request, int $id): RedirectResponse
    {
        try {
            // Haal medewerker op voor validatie
            $medewerker = $this->medewerkerRepository->getMedewerkerById($id);

            if ($medewerker === null) {
                Log::warning('Medewerker niet gevonden voor update.', [
                    'medewerkerId' => $id,
                ]);

                return redirect()->route('medewerkers.index')
                    ->with('error', 'Medewerker niet gevonden.');
            }

            // Haal gevalideerde data op
            $validated = $request->validated();

            // Werk medewerker bij via repository
            $this->medewerkerRepository->updateMedewerker(
                medewerkerId: $id,
                contactId: $medewerker['ContactId'],
                voornaam: $validated['voornaam'],
                tussenvoegsel: $validated['tussenvoegsel'] ?? null,
                achternaam: $validated['achternaam'],
                specialisatie: $validated['specialisatie'],
                geboortedatum: $validated['geboortedatum'],
                straatnaam: $validated['straatnaam'],
                huisnummer: $validated['huisnummer'],
                toevoeging: $validated['toevoeging'] ?? null,
                postcode: $validated['postcode'],
                plaats: $validated['plaats'],
                contactEmail: $validated['contact_email'],
                mobiel: $validated['mobiel'],
                opmerking: $validated['opmerking'] ?? null
            );

            // Log succesvolle update
            Log::info('Medewerker bijgewerkt.', [
                'medewerkerId' => $id,
            ]);

            return redirect()->route('medewerkers.show', $id)
                ->with('success', 'Medewerkergegevens bijgewerkt.');
        } catch (PDOException $exception) {
            Log::error('Databasefout bij bijwerken medewerker.', [
                'medewerkerId' => $id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('medewerkers.edit', $id)
                ->withInput()
                ->with('error', 'Er is een fout opgetreden bij het bijwerken van medewerkergegevens.');
        } catch (Throwable $exception) {
            Log::error('Onverwachte fout in MedewerkerController::update', [
                'medewerkerId' => $id,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return redirect()->route('medewerkers.edit', $id)
                ->withInput()
                ->with('error', 'Er is een onverwachte fout opgetreden.');
        }
    }
}
