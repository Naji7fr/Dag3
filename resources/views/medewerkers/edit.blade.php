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
        novalidate
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
            <div class="form-group">
                <label for="geboortedatum">Geboortedatum <span class="required">*</span></label>
                <input type="date" id="geboortedatum" name="geboortedatum"
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
                       value="{{ old('plaats', $medewerker['Plaats']) }}"
                       @class(['input-error' => $errors->has('plaats')]) required>
                @error('plaats')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <!-- Mobiel -->
            <div class="form-group">
                <label for="mobiel">Mobiel <span class="required">*</span></label>
                <input type="tel" id="mobiel" name="mobiel"
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

