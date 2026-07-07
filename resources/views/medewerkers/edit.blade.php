@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="breadcrumbs">
    <a href="{{ route('home') }}">Home</a> / <a href="{{ route('medewerkers.index') }}">Medewerkers</a> / Wijzigen
</div>

<h1 class="page-title">Medewerker wijzigen
    @if($medewerker['Tussenvoegsel'])
        {{ $medewerker['Voornaam'] }} {{ $medewerker['Tussenvoegsel'] }} {{ $medewerker['Achternaam'] }}
    @else
        {{ $medewerker['Voornaam'] }} {{ $medewerker['Achternaam'] }}
    @endif
</h1>

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="card form-card">
    <form
        method="post"
        action="{{ route('medewerkers.update', $medewerker['Id']) }}"
        id="medewerker-edit-form"
    >
        @csrf
        @method('PUT')

        <div class="form-grid">
            <!-- Naam (combined field for form compatibility) -->
            <div class="form-group">
                <label for="voornaam">Naam <span class="required">*</span></label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="voornaam" name="voornaam" placeholder="Voornaam"
                           value="{{ old('voornaam', $medewerker['Voornaam']) }}"
                           @class(['input-error' => $errors->has('voornaam')]) required style="flex: 2;">
                    <input type="text" id="tussenvoegsel" name="tussenvoegsel" placeholder="Tussenvoegsel"
                           value="{{ old('tussenvoegsel', $medewerker['Tussenvoegsel']) }}" style="flex: 1;">
                    <input type="text" id="achternaam" name="achternaam" placeholder="Achternaam"
                           value="{{ old('achternaam', $medewerker['Achternaam']) }}"
                           @class(['input-error' => $errors->has('achternaam')]) required style="flex: 2;">
                </div>
                @if($errors->has('voornaam') || $errors->has('achternaam'))
                    <div class="field-error">
                        @error('voornaam'){{ $message }}@enderror
                        @error('achternaam'){{ $message }}@enderror
                    </div>
                @endif
            </div>

            <!-- Specialisatie -->
            <div class="form-group">
                <label for="specialisatie">Specialisatie <span class="required">*</span></label>
                <div class="custom-dropdown @error('specialisatie') dropdown-error @enderror">
                    <select name="specialisatie" id="specialisatie"
                            @class(['input-error' => $errors->has('specialisatie')]) required>
                        <option value="">-- Selecteer specialisatie --</option>
                        @foreach($specialisaties as $spec)
                            <option value="{{ $spec['Specialisatie'] }}"
                                    @selected(old('specialisatie', $medewerker['Specialisatie']) === $spec['Specialisatie'])>
                                {{ $spec['Specialisatie'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('specialisatie')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <!-- Geboortedatum -->
            @php
                $geboortedatumMin = now()->subYears(config('kniploket.medewerker_max_age', 100))->format('Y-m-d');
                $geboortedatumMax = now()->subYears(config('kniploket.medewerker_min_age', 15))->format('Y-m-d');
            @endphp
            <div class="form-group">
                <label for="geboortedatum">Geboortedatum <span class="required">*</span></label>
                <input type="date" id="geboortedatum" name="geboortedatum"
                       min="{{ $geboortedatumMin }}"
                       max="{{ $geboortedatumMax }}"
                       title="Medewerker moet tussen 15 en 100 jaar oud zijn"
                       value="{{ old('geboortedatum', $medewerker['Geboortedatum']) }}"
                       @class(['input-error' => $errors->has('geboortedatum')]) required>
                @error('geboortedatum')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <!-- Contact e-mail -->
            <div class="form-group">
                <label for="contact_email">Contact e-mail <span class="required">*</span></label>
                <input type="email" id="contact_email" name="contact_email"
                       value="{{ old('contact_email', $medewerker['ContactEmail']) }}"
                       @class(['input-error' => $errors->has('contact_email')]) required>
                @error('contact_email')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <!-- Account e-mail -->
            <div class="form-group">
                <label for="account_email">Account e-mail</label>
                <input type="email" id="account_email" value="{{ $medewerker['AccountEmail'] }}" disabled>
            </div>

            <!-- Straatnaam -->
            <div class="form-group">
                <label for="straatnaam">Straatnaam <span class="required">*</span></label>
                <input type="text" id="straatnaam" name="straatnaam"
                       placeholder="Bijv. Winkel van Sinkelstraat"
                       pattern="([1-9][0-9]*e )?[A-Za-zÀ-ÿ'][A-Za-zÀ-ÿ\s'\-\.]{1,147}"
                       title="Voer een geldige Nederlandse straatnaam in die eindigt op straat, laan, weg, gracht, enz. (bijv. Oudegracht)"
                       maxlength="150"
                       value="{{ old('straatnaam', $medewerker['Straatnaam']) }}"
                       @class(['input-error' => $errors->has('straatnaam')]) required>
                @error('straatnaam')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row-3 form-section">
            <!-- Huisnummer -->
            <div class="form-group">
                <label for="huisnummer">Huisnummer <span class="required">*</span></label>
                <input type="text" id="huisnummer" name="huisnummer"
                       placeholder="Bijv. 88"
                       pattern="\d{1,5}[a-zA-Z]{0,2}"
                       title="Voer een geldig huisnummer in (bijv. 88 of 12A)"
                       maxlength="10"
                       value="{{ old('huisnummer', $medewerker['Huisnummer']) }}"
                       @class(['input-error' => $errors->has('huisnummer')]) required>
                @error('huisnummer')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <!-- Toevoeging -->
            <div class="form-group">
                <label for="toevoeging">Toevoeging</label>
                <input type="text" id="toevoeging" name="toevoeging"
                       value="{{ old('toevoeging', $medewerker['Toevoeging']) }}">
            </div>

            <!-- Postcode -->
            <div class="form-group">
                <label for="postcode">Postcode <span class="required">*</span></label>
                <input type="text" id="postcode" name="postcode"
                       placeholder="Bijv. 3512AB"
                       pattern="[1-9][0-9]{3}\s?[A-Za-z]{2}"
                       title="Voer een geldige Nederlandse postcode in (bijv. 3512AB)"
                       maxlength="10"
                       value="{{ old('postcode', $medewerker['Postcode']) }}"
                       @class(['input-error' => $errors->has('postcode')]) required>
                @error('postcode')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-grid form-section">
            <!-- Plaats -->
            <div class="form-group">
                <label for="plaats">Plaats <span class="required">*</span></label>
                <input type="text" id="plaats" name="plaats"
                       placeholder="Bijv. Utrecht"
                       pattern="[A-Za-zÀ-ÿ][A-Za-zÀ-ÿ\s'\-\.]{1,98}"
                       title="Voer een geldige Nederlandse plaatsnaam in (bijv. Utrecht)"
                       maxlength="100"
                       list="dutch-plaatsen-suggestions"
                       value="{{ old('plaats', $medewerker['Plaats']) }}"
                       @class(['input-error' => $errors->has('plaats')]) required>
                <datalist id="dutch-plaatsen-suggestions">
                    @foreach(config('kniploket.dutch_plaatsen') as $dutchPlaats)
                        <option value="{{ $dutchPlaats }}"></option>
                    @endforeach
                </datalist>
                @error('plaats')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <!-- Mobiel -->
            <div class="form-group">
                <label for="mobiel">Mobiel <span class="required">*</span></label>
                <input type="tel" id="mobiel" name="mobiel"
                       placeholder="Bijv. 06 12345678 of +31 6 12345678"
                       title="Voer een geldig Nederlands mobiel nummer in (begint met 06 of +31 6)"
                       maxlength="20"
                       value="{{ old('mobiel', $medewerker['Mobiel']) }}"
                       @class(['input-error' => $errors->has('mobiel')]) required>
                @error('mobiel')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-section" style="margin-top: 24px;">
            <!-- Opmerking -->
            <div class="form-group">
                <label for="opmerking">Opmerking</label>
                <input type="text" id="opmerking" name="opmerking"
                       value="{{ old('opmerking', $medewerker['Opmerking']) }}"
                       @class(['input-error' => $errors->has('opmerking')])>
                @error('opmerking')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-footer">
            <span>Velden met een * zijn verplicht.</span>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Opslaan</button>
                <a class="btn btn-secondary" href="{{ route('medewerkers.show', $medewerker['Id']) }}">Terug</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    window.KNIPLOKET = window.KNIPLOKET || {};
    window.KNIPLOKET.dutchPlaatsen = @json(array_map('mb_strtolower', config('kniploket.dutch_plaatsen')));
    window.KNIPLOKET.blockedAddressWords = @json(config('kniploket.blocked_address_words'));
    window.KNIPLOKET.straatSuffixes = @json(config('kniploket.straat_suffixes'));
    window.KNIPLOKET.medewerkerMinAge = {{ (int) config('kniploket.medewerker_min_age', 15) }};
    window.KNIPLOKET.medewerkerMaxAge = {{ (int) config('kniploket.medewerker_max_age', 100) }};
</script>
@endpush

