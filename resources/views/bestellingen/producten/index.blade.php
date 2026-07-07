@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="bestellingen-wrap">
<div class="breadcrumbs">
    <a href="{{ route('home') }}">Home</a> / <a href="{{ route('bestellingen.index') }}">Bestellingen</a> / Producten
</div>

<h1 class="page-title">Producten per bestelling</h1>

@if(!empty($successMessage))
    <div id="flash-alert" class="alert alert-success">{{ $successMessage }}</div>
@endif

@if(!empty($errorMessage))
    <div class="alert alert-error">{{ $errorMessage }}</div>
@endif

<div class="card table-card">
    <div class="table-meta">
        <span>Producten in bestelling {{ count($productRegels) }} product(en)</span>
    </div>

    <div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Categorie</th>
                <th>Merk</th>
                <th>Aantal</th>
                <th>Unit prijs</th>
                <th>Korting</th>
                <th>BTW %</th>
                <th>Totaal (Kort. + BTW)</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productRegels as $productRegel)
                <tr>
                    <td>{{ $productRegel['ProductNaam'] }}</td>
                    <td>{{ $productRegel['CategorieNaam'] }}</td>
                    <td>{{ $productRegel['Merk'] }}</td>
                    <td>{{ $productRegel['Aantal'] }}</td>
                    <td>{{ \App\Services\BestellingFormatter::formatEuro((float) $productRegel['UnitPrijs']) }}</td>
                    <td>{{ \App\Services\BestellingFormatter::formatEuro((float) $productRegel['Korting']) }}</td>
                    <td>{{ number_format((float) $productRegel['BTWPercentage'], 2, ',', '.') }}%</td>
                    <td>{{ \App\Services\BestellingFormatter::formatTotaal($productRegel) }}</td>
                    <td>
                        <a class="btn btn-outline" href="{{ route('bestellingen.producten.edit', [$bestelling['Id'], $productRegel['Id']]) }}">Wijzigen</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-message">Er zijn geen producten gevonden voor deze bestelling</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div class="detail-actions">
        <a class="btn btn-secondary" href="{{ route('bestellingen.index') }}">Terug</a>
    </div>
</div>
</div>
@endsection
