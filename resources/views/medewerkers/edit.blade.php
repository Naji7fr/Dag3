@extends('layouts.app')

@section('content')
<div class="breadcrumbs">
    <a href="{{ route('home') }}">Home</a> / <a href="{{ route('medewerkers.index') }}">Medewerkers</a> / Wijzigen
</div>

<h1 class="page-title">Medewerker wijzigen</h1>

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-error">
        Medewerkergegevens zijn niet bijgewerkt
    </div>
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
            <div class="form-group">
                <label for="voornaam">Voornaam <span class="required">*</span></label>
                <input type="text" id="voornaam" name="voornaam"
                       value="{{ old('voornaam', $medewerker['Voornaam']) }}"
                       @class(['input-error' => $errors->has('voornaam')]) required>
                @error('voornaam')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="tussenvoegsel">Tussenvoegsel</label>
                <input type="text" id="tussenvoegsel" name="tussenvoegsel"
                       value="{{ old('tussenvoegsel', $medewerker['Tussenvoegsel']) }}"
                       @class(['input-error' => $errors->has('tussenvoegsel')])>
                @error('tussenvoegsel')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="achternaam">Achternaam <span class="required">*</span></label>
                <input type="text" id="achternaam" name="achternaam"
                       value="{{ old('achternaam', $medewerker['Achternaam']) }}"
                       @class(['input-error' => $errors->has('achternaam')]) required>
                @error('achternaam')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row-3 form-section">
            <div class="form-group">
                <label for="specialisatie">Specialisatie <span class="required">*</span></label>
                <div @class(['custom-dropdown' => true, 'dropdown-error' => $errors->has('specialisatie')])>
                    <select id="specialisatie" name="specialisatie"
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
            <div class="form-group">
                <label for="geboortedatum">Geboortedatum <span class="required">*</span></label>
                <input type="date" id="geboortedatum" name="geboortedatum"
                       value="{{ old('geboortedatum', $medewerker['Geboortedatum']) }}"
                       @class(['input-error' => $errors->has('geboortedatum')]) required>
                @error('geboortedatum')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-grid form-section">
            <div class="form-group">
                <label for="contact_email">Contact e-mail <span class="required">*</span></label>
                <input type="email" id="contact_email" name="contact_email"
                       value="{{ old('contact_email', $medewerker['ContactEmail']) }}"
                       @class(['input-error' => $errors->has('contact_email')]) required>
                @error('contact_email')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="account_email">Account e-mail</label>
                <input type="email" id="account_email" value="{{ $medewerker['AccountEmail'] }}" disabled>
            </div>
        </div>

        <div class="form-row-3 form-section">
            <div class="form-group">
                <label for="straatnaam">Straatnaam <span class="required">*</span></label>
                <input type="text" id="straatnaam" name="straatnaam"
                       value="{{ old('straatnaam', $medewerker['Straatnaam']) }}"
                       @class(['input-error' => $errors->has('straatnaam')]) required>
                @error('straatnaam')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="huisnummer">Huisnummer <span class="required">*</span></label>
                <input type="text" id="huisnummer" name="huisnummer"
                       value="{{ old('huisnummer', $medewerker['Huisnummer']) }}"
                       @class(['input-error' => $errors->has('huisnummer')]) required>
                @error('huisnummer')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="toevoeging">Toevoeging</label>
                <input type="text" id="toevoeging" name="toevoeging"
                       value="{{ old('toevoeging', $medewerker['Toevoeging']) }}"
                       @class(['input-error' => $errors->has('toevoeging')])>
                @error('toevoeging')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-grid form-section">
            <div class="form-group">
                <label for="postcode">Postcode <span class="required">*</span></label>
                <input type="text" id="postcode" name="postcode"
                       value="{{ old('postcode', $medewerker['Postcode']) }}"
                       @class(['input-error' => $errors->has('postcode')]) required>
                @error('postcode')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="plaats">Plaats <span class="required">*</span></label>
                <input type="text" id="plaats" name="plaats"
                       value="{{ old('plaats', $medewerker['Plaats']) }}"
                       @class(['input-error' => $errors->has('plaats')]) required>
                @error('plaats')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full-width">
                <label for="mobiel">Mobiel <span class="required">*</span></label>
                <input type="text" id="mobiel" name="mobiel"
                       value="{{ old('mobiel', $medewerker['Mobiel']) }}"
                       @class(['input-error' => $errors->has('mobiel')]) required>
                @error('mobiel')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Opslaan</button>
            <a href="{{ route('medewerkers.show', $medewerker['Id']) }}" class="btn btn-outline">Terug</a>
        </div>
    </form>
</div>

<script>
    // Auto-hide success message after 3 seconds
    document.addEventListener('DOMContentLoaded', function () {
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(function () {
                successAlert.style.transition = 'opacity 0.3s ease-out';
                successAlert.style.opacity = '0';
                setTimeout(function () {
                    successAlert.remove();
                }, 300);
            }, 3000);
        }
    });
</script>
@endsection

