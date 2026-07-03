@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="breadcrumbs">
    <a href="{{ route('home') }}">Home</a> / Medewerkers
</div>

<h1 class="page-title">Overzicht medewerkers</h1>

@if(!empty($successMessage))
    <div id="flash-alert" class="alert alert-success">{{ $successMessage }}</div>
@endif

@if(!empty($errorMessage))
    <div class="alert alert-error">{{ $errorMessage }}</div>
@endif

<div class="card search-card">
    <form method="get" action="{{ route('medewerkers.index') }}" id="specialisatie-filter-form" novalidate>
        <label for="specialisatie">Specialisatie</label>
        <div class="search-row">
            <div class="custom-dropdown">
                <select
                    id="specialisatie"
                    name="specialisatie"
                    @class(['input-error' => $errors->has('specialisatie')])
                >
                    <option value="">Alle specialisaties</option>
                    @foreach($specialisaties as $spec)
                        <option value="{{ $spec['Specialisatie'] }}"
                            @selected(old('specialisatie', $specialisatie ?? '') === $spec['Specialisatie'])
                        >
                            {{ $spec['Specialisatie'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Toon medewerkers</button>
            <a href="{{ route('medewerkers.index') }}" class="btn btn-secondary">Reset</a>
        </div>
        @error('specialisatie')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </form>
</div>

<div class="card table-card">
    <div class="table-meta">
        <span>
            Gevonden medewerkers - 
            @if($medewerkers->total() > 0)
                {{ $medewerkers->total() }} medewerker(s)
            @else
                0 medewerkers
            @endif
        </span>
        @if($medewerkers->lastPage() > 1)
            @include('partials.pagination', ['paginator' => $medewerkers])
        @endif
    </div>

    <div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Naam</th>
                <th>Specialisatie</th>
                <th>Adres</th>
                <th>Postcode</th>
                <th>Woonplaats</th>
                <th>Mobiel</th>
                <th>Contact e-mail</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            @forelse($medewerkers as $medewerkerRecord)
                <tr>
                    <td>
                        @if($medewerkerRecord['Tussenvoegsel'])
                            {{ $medewerkerRecord['Voornaam'] }} {{ $medewerkerRecord['Tussenvoegsel'] }} {{ $medewerkerRecord['Achternaam'] }}
                        @else
                            {{ $medewerkerRecord['Voornaam'] }} {{ $medewerkerRecord['Achternaam'] }}
                        @endif
                    </td>
                    <td>{{ $medewerkerRecord['Specialisatie'] }}</td>
                    <td>
                        @php
                            $adres = $medewerkerRecord['Straatnaam'] . ' ' . $medewerkerRecord['Huisnummer'];
                            if ($medewerkerRecord['Toevoeging']) {
                                $adres .= ' ' . $medewerkerRecord['Toevoeging'];
                            }
                        @endphp
                        {{ $adres }}
                    </td>
                    <td>{{ $medewerkerRecord['Postcode'] }}</td>
                    <td>{{ $medewerkerRecord['Plaats'] }}</td>
                    <td>{{ $medewerkerRecord['Mobiel'] }}</td>
                    <td>{{ $medewerkerRecord['ContactEmail'] }}</td>
                    <td>
                        <a class="btn btn-outline" href="{{ route('medewerkers.show', $medewerkerRecord['Id']) }}">Details</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-message">
                        @if(!empty($specialisatie))
                            Er zijn geen medewerkers bekend met de geselecteerde specialisatie
                        @else
                            Er zijn geen medewerkers gevonden
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@push('scripts')
<script>
    // Auto-hide flash message na {{ $flashAutoHideMs }}ms als aanwezig
    @if($autoHideFlash)
    document.addEventListener('DOMContentLoaded', function() {
        const flashAlert = document.getElementById('flash-alert');
        if (flashAlert) {
            setTimeout(() => {
                flashAlert.style.opacity = '0';
                flashAlert.style.transition = 'opacity 0.3s ease';
                setTimeout(() => flashAlert.remove(), 300);
            }, {{ $flashAutoHideMs }});
        }
    });
    @endif
</script>
@endpush
@endsection
