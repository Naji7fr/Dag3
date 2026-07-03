@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="breadcrumbs">
    <a href="{{ route('home') }}">Home</a> / Klanten
</div>

<h1 class="page-title">Overzicht klanten</h1>

@if(!empty($successMessage))
    <div id="flash-alert" class="alert alert-success">{{ $successMessage }}</div>
@endif

@if(!empty($errorMessage))
    <div class="alert alert-error">{{ $errorMessage }}</div>
@endif

<div class="card search-card">
    <form method="get" action="{{ route('klanten.index') }}" id="postcode-search-form" novalidate>
        <label for="postcode">Postcode zoeken</label>
        <div class="search-row">
            <input
                type="text"
                id="postcode"
                name="postcode"
                placeholder="Bijv. 3512AB"
                pattern="[1-9][0-9]{3}\s?[A-Za-z]{2}"
                title="Voer een geldige Nederlandse postcode in (bijv. 3512AB)"
                value="{{ old('postcode', $postcode ?? '') }}"
                @class(['input-error' => $errors->has('postcode')])
            >
            <button type="submit" class="btn btn-primary">Toon klanten</button>
            <a href="{{ route('klanten.index') }}" class="btn btn-secondary">Reset</a>
        </div>
        @error('postcode')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </form>
</div>

<div class="card table-card">
    <div class="table-meta">
        <span>Gevonden klanten - {{ $klanten->total() }} klant(en)</span>
        @if($klanten->lastPage() > 1)
            @include('partials.pagination', ['paginator' => $klanten])
        @endif
    </div>

    <div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Naam</th>
                <th>Relatienummer</th>
                <th>Adres</th>
                <th>Postcode</th>
                <th>Woonplaats</th>
                <th>Mobiel</th>
                <th>Contact e-mail</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            @forelse($klanten as $klantRecord)
                <tr>
                    <td>{{ \App\Services\KlantFormatter::formatNaam($klantRecord) }}</td>
                    <td>{{ $klantRecord['Relatienummer'] }}</td>
                    <td>{{ \App\Services\KlantFormatter::formatAdres($klantRecord) }}</td>
                    <td>{{ $klantRecord['Postcode'] }}</td>
                    <td>{{ $klantRecord['Plaats'] }}</td>
                    <td>{{ $klantRecord['Mobiel'] }}</td>
                    <td>{{ $klantRecord['ContactEmail'] }}</td>
                    <td>
                        <a class="btn btn-outline" href="{{ route('klanten.show', $klantRecord['Id']) }}">Details</a>
                        <a class="btn btn-outline" href="{{ route('klanten.edit', $klantRecord['Id']) }}">Wijzigen</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-message">
                        @if(!empty($postcode))
                            Er zijn geen klanten bekend die de geselecteerde postcode hebben
                        @else
                            Er zijn geen klanten gevonden
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
