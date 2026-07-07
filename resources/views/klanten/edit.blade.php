@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="breadcrumbs">
    <a href="{{ route('home') }}">Home</a> / <a href="{{ route('klanten.index') }}">Klanten</a> / Wijzigen
</div>

<h1 class="page-title">Klant wijzigen {{ \App\Services\KlantFormatter::formatNaam($klant) }}</h1>

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="card form-card">
    <form
        method="post"
        action="{{ route('klanten.update', $klant['Id']) }}"
        id="klant-edit-form"
    >
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label for="naam">Naam <span class="required">*</span></label>
                <input type="text" id="naam" name="naam" maxlength="150"
                       value="{{ old('naam', $formData['naam']) }}"
                       @class(['input-error' => $errors->has('naam')]) required>
                @error('naam')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="relatienummer">Relatienummer</label>
                <input type="text" id="relatienummer" value="{{ $klant['Relatienummer'] }}" disabled>
            </div>
            <div class="form-group">
                <label for="contact_email">Contact e-mail <span class="required">*</span></label>
                <input type="email" id="contact_email" name="contact_email"
                       value="{{ old('contact_email', $formData['contact_email']) }}"
                       @class(['input-error' => $errors->has('contact_email')]) required>
                @error('contact_email')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="account_email">Account e-mail</label>
                <input type="email" id="account_email" value="{{ $klant['AccountEmail'] }}" disabled>
            </div>
        </div>

        <div class="form-row-3 form-section">
            <div class="form-group">
                <label for="straatnaam">Straatnaam <span class="required">*</span></label>
                <input type="text" id="straatnaam" name="straatnaam"
                       placeholder="Bijv. Winkel van Sinkelstraat"
                       pattern="([1-9][0-9]*e )?[A-Za-zÀ-ÿ'][A-Za-zÀ-ÿ\s'\-\.]{1,147}"
                       title="Voer een geldige Nederlandse straatnaam in die eindigt op straat, laan, weg, gracht, enz. (bijv. Oudegracht)"
                       maxlength="150"
                       value="{{ old('straatnaam', $formData['straatnaam']) }}"
                       @class(['input-error' => $errors->has('straatnaam')]) required>
                @error('straatnaam')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="huisnummer">Huisnummer <span class="required">*</span></label>
                <input type="text" id="huisnummer" name="huisnummer"
                       placeholder="Bijv. 88"
                       pattern="\d{1,5}[a-zA-Z]{0,2}"
                       title="Voer een geldig huisnummer in (bijv. 88 of 12A)"
                       maxlength="10"
                       value="{{ old('huisnummer', $formData['huisnummer']) }}"
                       @class(['input-error' => $errors->has('huisnummer')]) required>
                @error('huisnummer')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="toevoeging">Toevoeging</label>
                <input type="text" id="toevoeging" name="toevoeging"
                       value="{{ old('toevoeging', $formData['toevoeging']) }}">
            </div>
        </div>

        <div class="form-grid form-section">
            <div class="form-group">
                <label for="postcode">Postcode <span class="required">*</span></label>
                <input type="text" id="postcode" name="postcode"
                       placeholder="Bijv. 3512AB"
                       pattern="[1-9][0-9]{3}\s?[A-Za-z]{2}"
                       title="Voer een geldige Nederlandse postcode in (bijv. 3512AB)"
                       maxlength="10"
                       value="{{ old('postcode', $formData['postcode']) }}"
                       @class(['input-error' => $errors->has('postcode')]) required>
                @error('postcode')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="plaats">Plaats <span class="required">*</span></label>
                <input type="text" id="plaats" name="plaats"
                       placeholder="Bijv. Utrecht"
                       pattern="[A-Za-zÀ-ÿ][A-Za-zÀ-ÿ\s'\-\.]{1,98}"
                       title="Voer een geldige Nederlandse plaatsnaam in (bijv. Utrecht)"
                       maxlength="100"
                       list="dutch-plaatsen-suggestions"
                       value="{{ old('plaats', $formData['plaats']) }}"
                       @class(['input-error' => $errors->has('plaats')]) required>
                <datalist id="dutch-plaatsen-suggestions">
                    @foreach(config('kniploket.dutch_plaatsen') as $dutchPlaats)
                        <option value="{{ $dutchPlaats }}"></option>
                    @endforeach
                </datalist>
                @error('plaats')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full-width">
                <label for="mobiel">Mobiel <span class="required">*</span></label>
                <input type="tel" id="mobiel" name="mobiel"
                       placeholder="Bijv. 06 12345678 of +31 6 12345678"
                       title="Voer een geldig Nederlands mobiel nummer in (begint met 06 of +31 6)"
                       maxlength="20"
                       value="{{ old('mobiel', $formData['mobiel']) }}"
                       @class(['input-error' => $errors->has('mobiel')]) required>
                @error('mobiel')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full-width">
                <label for="bijzonderheden">Bijzonderheden</label>
                <input type="text" id="bijzonderheden" name="bijzonderheden" maxlength="500"
                       value="{{ old('bijzonderheden', $formData['bijzonderheden']) }}"
                       @class(['input-error' => $errors->has('bijzonderheden')])>
                @error('bijzonderheden')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-footer">
            <span>Velden met een * zijn verplicht.</span>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Opslaan</button>
                <a class="btn btn-secondary" href="{{ route('klanten.show', $klant['Id']) }}">Terug</a>
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
</script>
@endpush
