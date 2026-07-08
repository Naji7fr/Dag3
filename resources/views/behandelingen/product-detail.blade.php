@extends('layouts.app')

@section('title', 'Productdetail - ' . $product->Naam)

@section('content')
<section class="product-detail-overzicht">
    <div class="breadcrumbs">
        <a href="{{ route('home') }}">Home</a> /
        <a href="{{ route('behandelingen.index') }}">Behandelingen</a> /
        Detail
    </div>

    <h1 class="product-detail-title">
        Productdetail
        <span>{{ $product->Naam }}</span>
    </h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @php
        $terugLink = $behandelingId ? route('behandelingen.show', $behandelingId) : route('behandelingen.index');
    @endphp

    <div class="card product-detail-card">
        <table class="product-detail-table">
            <tbody>
                <tr><th>Product</th><td>{{ $product->Naam }}</td></tr>
                <tr><th>Merk</th><td>{{ $product->Merk ?? '-' }}</td></tr>
                <tr><th>Omschrijving</th><td>{{ $product->Omschrijving ?? '-' }}</td></tr>
                <tr><th>EAN-code</th><td>{{ $product->EANcode ?? '-' }}</td></tr>
                <tr>
                    <th>Houdbaarheidsdatum</th>
                    <td>{{ $product->Houdbaarheidsdatum ? \Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('d-m-Y') : '-' }}</td>
                </tr>
                <tr><th>Inkoopprijs</th><td>EUR {{ number_format($product->InkoopPrijs, 2, ',', '.') }}</td></tr>
                <tr><th>Verkoopprijs</th><td>EUR {{ number_format($product->VerkoopPrijs, 2, ',', '.') }}</td></tr>
                <tr><th>Aantal op voorraad</th><td>{{ $product->AantalOpVoorraad ?? 0 }}</td></tr>
                <tr><th>Leverancier</th><td>{{ $leverancier->Naam ?? '-' }}</td></tr>
                <tr><th>Postcode leverancier</th><td>{{ $leverancier->Postcode ?? '-' }}</td></tr>
                <tr><th>Plaats leverancier</th><td>{{ $leverancier->Plaats ?? '-' }}</td></tr>
                <tr><th>E-mail leverancier</th><td>{{ $leverancier->Email ?? '-' }}</td></tr>
                <tr><th>Mobiel leverancier</th><td>{{ $leverancier->Mobiel ?? '-' }}</td></tr>
                <tr><th>Opmerking</th><td>{{ $product->Opmerking ?? '-' }}</td></tr>
            </tbody>
        </table>

        <div class="product-detail-actions">
            <a href="{{ route('behandelingen.edit-product', $product->Id) }}" class="btn btn-primary">Wijzigen</a>
            <a href="{{ $terugLink }}" class="btn btn-outline">Terug</a>
        </div>
    </div>
</section>
@endsection
