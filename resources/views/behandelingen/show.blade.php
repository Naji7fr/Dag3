@extends('layouts.app')

@section('title', 'Producten - ' . $behandeling->Naam)

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <a href="{{ route('behandelingen.index') }}">Behandelingen</a> / 
    {{ $behandeling->Naam }}
</div>

<h1 style="color: #D32F2F;">{{ $behandeling->Naam }}</h1>
<p style="margin-bottom: 20px;">Omschrijving: {{ $behandeling->Omschrijving }}</p>

@if($producten->count() > 0)
    <div class="page-info">
        Aantal producten: {{ $producten->count() }}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Productnaam</th>
                <th>Merk</th>
                <th>EAN-code</th>
                <th>Inkoopprijs</th>
                <th>Verkoopprijs</th>
                <th>Aantal op voorraad</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            @foreach($producten as $product)
                <tr>
                    <td>{{ $product->Naam }}</td>
                    <td>{{ $product->Merk ?? '-' }}</td>
                    <td>{{ $product->EANcode ?? '-' }}</td>
                    <td>EUR {{ number_format($product->InkoopPrijs, 2, ',', '.') }}</td>
                    <td>EUR {{ number_format($product->VerkoopPrijs, 2, ',', '.') }}</td>
                    <td>{{ $product->AantalOpVoorraad ?? 0 }}</td>
                    <td>
                        <a href="{{ route('behandelingen.edit-product', $product->Id) }}" class="btn btn-small btn-primary">
                            Wijzigen
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="text-align: right; margin-top: 20px;">
        <a href="{{ route('behandelingen.index') }}" class="btn btn-secondary">Terug</a>
    </div>
@else
    <div class="alert alert-warning">
        Er zijn geen producten voor deze behandeling
    </div>
    
    <div style="text-align: right; margin-top: 20px;">
        <a href="{{ route('behandelingen.index') }}" class="btn btn-secondary">Terug</a>
    </div>
@endif
@endsection
