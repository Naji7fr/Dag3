@extends('layouts.app')

@section('title', 'Overzicht Behandelingen')

@section('content')
<div class="breadcrumbs">
    <a href="{{ route('home') }}">Home</a> / Behandelingen
</div>

<h1 class="page-title">Overzicht behandelingen</h1>

<div class="card search-card">
    <form method="get" action="{{ route('behandelingen.index') }}" id="behandeling-filter-form">
        <label for="behandeling">Behandeling selecteren</label>
        <div class="search-row">
            <select id="behandeling" name="behandeling" class="custom-dropdown">
                <option value="">Alle behandelingen</option>
                @foreach($allBehandelingen as $b)
                    <option value="{{ $b->Naam }}" @if($selectedBehandeling === $b->Naam) selected @endif>
                        {{ $b->Naam }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Maak selectie</button>
            <a href="{{ route('behandelingen.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="card table-card">
    <div class="table-meta">
        <span>Gevonden behandelingen - {{ $behandelingen->total() }} behandeling(en)</span>
        @if($behandelingen->lastPage() > 1)
            @include('partials.pagination', ['paginator' => $behandelingen])
        @endif
    </div>

    <div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Soort</th>
                <th>Omschrijving</th>
                <th>Duur</th>
                <th>Prijs</th>
                <th>Aantal producten</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            @forelse($behandelingen as $behandeling)
                <tr>
                    <td>{{ $behandeling->Naam }}</td>
                    <td>{{ $behandeling->Omschrijving }}</td>
                    <td>{{ $behandeling->Duurminuten }} min</td>
                    <td>EUR {{ number_format($behandeling->Prijs, 2, ',', '.') }}</td>
                    <td>{{ $behandeling->aantal_producten }}</td>
                    <td>
                        <a class="btn btn-outline" href="{{ route('behandelingen.show', $behandeling->Id) }}">Producten</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-message">Er zijn geen behandelingen bekend met deze naam</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
