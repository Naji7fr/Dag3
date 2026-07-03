@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="breadcrumbs">
    <a href="{{ route('home') }}">Home</a> /
    <a href="{{ route('bestellingen.index') }}">Bestellingen</a> /
    <a href="{{ route('bestellingen.producten', $bestelling['Id']) }}">Producten</a> / Wijzigen
</div>

<h1 class="page-title">Bestelproduct wijzigen</h1>

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="card form-card">
    <form
        method="post"
        action="{{ route('bestellingen.producten.update', [$bestelling['Id'], $productRegel['Id']]) }}"
        id="bestelproduct-edit-form"
        novalidate
        data-unit-prijs="{{ $productRegel['UnitPrijs'] }}"
        data-korting="{{ $productRegel['Korting'] }}"
        data-btw="{{ $productRegel['BTWPercentage'] }}"
    >
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label for="bestelnummer">Bestelnummer</label>
                <input type="text" id="bestelnummer" value="{{ $bestelling['BestelNummer'] }}" disabled>
            </div>
            <div class="form-group">
                <label for="bestelstatus">Status</label>
                <input type="text" id="bestelstatus" value="{{ $bestelling['Bestelstatus'] }}" disabled>
            </div>
            <div class="form-group">
                <label for="productnaam">Product</label>
                <input type="text" id="productnaam" value="{{ $productRegel['ProductNaam'] }}" disabled>
            </div>
            <div class="form-group">
                <label for="categorie">Categorie</label>
                <input type="text" id="categorie" value="{{ $productRegel['CategorieNaam'] }}" disabled>
            </div>
            <div class="form-group">
                <label for="merk">Merk</label>
                <input type="text" id="merk" value="{{ $productRegel['Merk'] }}" disabled>
            </div>
            <div class="form-group">
                <label for="unit_prijs">Unit prijs</label>
                <input type="text" id="unit_prijs" value="{{ \App\Services\BestellingFormatter::formatEuro((float) $productRegel['UnitPrijs']) }}" disabled>
            </div>
            <div class="form-group">
                <label for="korting">Korting</label>
                <input type="text" id="korting" value="{{ \App\Services\BestellingFormatter::formatEuro((float) $productRegel['Korting']) }}" disabled>
            </div>
            <div class="form-group">
                <label for="btw">BTW %</label>
                <input type="text" id="btw" value="{{ number_format((float) $productRegel['BTWPercentage'], 2, ',', '.') }}%" disabled>
            </div>
            <div class="form-group">
                <label for="aantal">Aantal <span class="required">*</span></label>
                <input
                    type="number"
                    id="aantal"
                    name="aantal"
                    min="1"
                    step="1"
                    value="{{ old('aantal', $productRegel['Aantal']) }}"
                    @class(['input-error' => $errors->has('aantal')])
                    required
                >
                @error('aantal')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="totaal">Totaal (Kort. + BTW)</label>
                <input type="text" id="totaal" value="{{ \App\Services\BestellingFormatter::formatEuro($totaal) }}" disabled>
            </div>
        </div>

        <div class="form-footer">
            <span>Velden met een * zijn verplicht.</span>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Opslaan</button>
                <a class="btn btn-secondary" href="{{ route('bestellingen.producten', $bestelling['Id']) }}">Terug</a>
            </div>
        </div>
    </form>
</div>
@endsection
