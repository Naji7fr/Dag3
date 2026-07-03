@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="breadcrumbs">
    <a href="{{ route('home') }}">Home</a> / Bestellingen
</div>

<h1 class="page-title">Overzicht bestellingen</h1>

<div class="card search-card">
    <form method="get" action="{{ route('bestellingen.index') }}" id="status-filter-form" novalidate>
        <label for="status">Status</label>
        <div class="search-row">
            <select
                id="status"
                name="status"
                @class(['input-error' => $errors->has('status')])
            >
                <option value="">Alle statussen</option>
                @foreach($statussen as $statusOptie)
                    <option value="{{ $statusOptie }}" @selected(old('status', $status ?? '') === $statusOptie)>
                        {{ $statusOptie }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Maak selectie</button>
            <a href="{{ route('bestellingen.index') }}" class="btn btn-secondary">Reset</a>
        </div>
        @error('status')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </form>
</div>

<div class="card table-card">
    <div class="table-meta">
        <span>Gevonden bestellingen - {{ $bestellingen->total() }} bestelling(en)</span>
        @if($bestellingen->lastPage() > 1)
            @include('partials.pagination', ['paginator' => $bestellingen])
        @endif
    </div>

    <div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Bestelnummer</th>
                <th>Klant</th>
                <th>Datum</th>
                <th>Tijd</th>
                <th>Status</th>
                <th>Producten</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bestellingen as $bestelling)
                <tr>
                    <td>{{ $bestelling['BestelNummer'] }}</td>
                    <td>{{ \App\Services\BestellingFormatter::formatKlantNaam($bestelling) }}</td>
                    <td>{{ \App\Services\BestellingFormatter::formatDatum($bestelling['Datum'] ?? '') }}</td>
                    <td>{{ \App\Services\BestellingFormatter::formatTijd($bestelling['Tijd'] ?? '') }}</td>
                    <td>{{ $bestelling['Bestelstatus'] }}</td>
                    <td>{{ $bestelling['Producten'] ?? '' }}</td>
                    <td>
                        <span class="btn btn-outline btn-disabled">Producten</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-message">
                        @if(!empty($status))
                            Er zijn geen bestellingen bekend met deze status
                        @else
                            Er zijn geen bestellingen gevonden
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
