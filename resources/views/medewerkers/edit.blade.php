@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-4">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav mb-4">
        <a href="{{ route('home') }}">Home</a>
        <span> / </span>
        <a href="{{ route('medewerkers.index') }}">Medewerkers</a>
        <span> / </span>
        <span>Wijzigen</span>
    </nav>

    <!-- Title -->
    <h1 class="mb-4">Medewerker wijzigen</h1>

    <!-- Error Message (Form Level) -->
    @if ($errors->any() && !$errors->has('specialisatie'))
        <div class="alert alert-error">
            Medewerkergegevens zijn niet bijgewerkt
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('medewerkers.update', $medewerker['Id']) }}" method="POST" class="edit-form">
        @csrf
        @method('PUT')

        <div class="form-row">
            <!-- Voornaam -->
            <div class="form-group">
                <label for="voornaam">Voornaam</label>
                <input
                    type="text"
                    name="voornaam"
                    id="voornaam"
                    class="form-control @error('voornaam') input-error @enderror"
                    value="{{ old('voornaam', $medewerker['Voornaam']) }}"
                    required
                />
                @error('voornaam')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tussenvoegsel -->
            <div class="form-group">
                <label for="tussenvoegsel">Tussenvoegsel</label>
                <input
                    type="text"
                    name="tussenvoegsel"
                    id="tussenvoegsel"
                    class="form-control"
                    value="{{ old('tussenvoegsel', $medewerker['Tussenvoegsel']) }}"
                />
            </div>

            <!-- Achternaam -->
            <div class="form-group">
                <label for="achternaam">Achternaam</label>
                <input
                    type="text"
                    name="achternaam"
                    id="achternaam"
                    class="form-control @error('achternaam') input-error @enderror"
                    value="{{ old('achternaam', $medewerker['Achternaam']) }}"
                    required
                />
                @error('achternaam')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <!-- Specialisatie -->
            <div class="form-group">
                <label for="specialisatie">Specialisatie</label>
                <div class="custom-dropdown @error('specialisatie') dropdown-error @enderror">
                    <select
                        name="specialisatie"
                        id="specialisatie"
                        class="form-control @error('specialisatie') input-error @enderror"
                        required
                    >
                        <option value="">-- Selecteer specialisatie --</option>
                        @foreach($specialisaties as $spec)
                            <option
                                value="{{ $spec['Specialisatie'] }}"
                                @selected(old('specialisatie', $medewerker['Specialisatie']) === $spec['Specialisatie'])
                            >
                                {{ $spec['Specialisatie'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('specialisatie')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Geboortedatum -->
            <div class="form-group">
                <label for="geboortedatum">Geboortedatum</label>
                <input
                    type="date"
                    name="geboortedatum"
                    id="geboortedatum"
                    class="form-control @error('geboortedatum') input-error @enderror"
                    value="{{ old('geboortedatum', $medewerker['Geboortedatum']) }}"
                    required
                />
                @error('geboortedatum')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <!-- Contact e-mail -->
            <div class="form-group">
                <label for="contact_email">Contact e-mail</label>
                <input
                    type="email"
                    name="contact_email"
                    id="contact_email"
                    class="form-control @error('contact_email') input-error @enderror"
                    value="{{ old('contact_email', $medewerker['ContactEmail']) }}"
                    required
                />
                @error('contact_email')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Account e-mail (readonly) -->
            <div class="form-group">
                <label for="account_email">Account e-mail</label>
                <input
                    type="email"
                    id="account_email"
                    class="form-control"
                    value="{{ $medewerker['AccountEmail'] }}"
                    readonly
                />
            </div>
        </div>

        <div class="form-row">
            <!-- Straatnaam -->
            <div class="form-group">
                <label for="straatnaam">Straatnaam</label>
                <input
                    type="text"
                    name="straatnaam"
                    id="straatnaam"
                    class="form-control @error('straatnaam') input-error @enderror"
                    value="{{ old('straatnaam', $medewerker['Straatnaam']) }}"
                    required
                />
                @error('straatnaam')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Huisnummer -->
            <div class="form-group">
                <label for="huisnummer">Huisnummer</label>
                <input
                    type="text"
                    name="huisnummer"
                    id="huisnummer"
                    class="form-control @error('huisnummer') input-error @enderror"
                    value="{{ old('huisnummer', $medewerker['Huisnummer']) }}"
                    required
                />
                @error('huisnummer')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Toevoeging -->
            <div class="form-group">
                <label for="toevoeging">Toevoeging</label>
                <input
                    type="text"
                    name="toevoeging"
                    id="toevoeging"
                    class="form-control"
                    value="{{ old('toevoeging', $medewerker['Toevoeging']) }}"
                />
            </div>
        </div>

        <div class="form-row">
            <!-- Postcode -->
            <div class="form-group">
                <label for="postcode">Postcode</label>
                <input
                    type="text"
                    name="postcode"
                    id="postcode"
                    class="form-control @error('postcode') input-error @enderror"
                    value="{{ old('postcode', $medewerker['Postcode']) }}"
                    required
                />
                @error('postcode')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Plaats -->
            <div class="form-group">
                <label for="plaats">Plaats</label>
                <input
                    type="text"
                    name="plaats"
                    id="plaats"
                    class="form-control @error('plaats') input-error @enderror"
                    value="{{ old('plaats', $medewerker['Plaats']) }}"
                    required
                />
                @error('plaats')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Mobiel -->
            <div class="form-group">
                <label for="mobiel">Mobiel</label>
                <input
                    type="text"
                    name="mobiel"
                    id="mobiel"
                    class="form-control @error('mobiel') input-error @enderror"
                    value="{{ old('mobiel', $medewerker['Mobiel']) }}"
                    required
                />
                @error('mobiel')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Form Buttons -->
        <div class="form-actions mt-4">
            <button type="submit" class="btn btn-primary">Opslaan</button>
            <a href="{{ route('medewerkers.show', $medewerker['Id']) }}" class="btn btn-secondary">Terug</a>
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
