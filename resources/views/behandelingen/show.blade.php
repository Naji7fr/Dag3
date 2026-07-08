@extends('layouts.app')

@section('title', 'Producten - ' . $behandeling->Naam)

@section('content')
<section class="behandeling-producten-overzicht">
    <div class="breadcrumbs">
        <a href="{{ route('home') }}">Home</a> /
        <a href="{{ route('behandelingen.index') }}">Behandelingen</a> /
        Detail
    </div>

    <h1 class="product-overview-title">
        Producten per behandeling
        <span>{{ $behandeling->Naam }}</span>
    </h1>

    <div class="card behandeling-producten-card">
        @if($producten->count() > 0)
            <div class="table-responsive">
                <table class="data-table behandeling-producten-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Merk</th>
                            <th>Omschrijving</th>
                            <th>EAN-code</th>
                            <th>Aantal op voorraad</th>
                            <th>Verkoopprijs</th>
                            <th>Actie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($producten as $product)
                            <tr>
                                <td>{{ $product->Naam }}</td>
                                <td>{{ $product->Merk ?? '-' }}</td>
                                <td>{{ $product->Omschrijving ?? '-' }}</td>
                                <td>{{ $product->EANcode ?? '-' }}</td>
                                <td>{{ $product->AantalOpVoorraad ?? 0 }}</td>
                                <td>EUR {{ number_format($product->VerkoopPrijs, 2, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('behandelingen.product-detail', $product->Id) }}" class="btn btn-primary btn-detail-link">Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="behandeling-producten-actions">
                <a href="{{ route('behandelingen.index') }}" class="btn btn-outline">Terug</a>
            </div>
        @else
            <div class="alert alert-info">
                Er zijn geen producten voor deze behandeling.
            </div>
            <div class="behandeling-producten-actions">
                <a href="{{ route('behandelingen.index') }}" class="btn btn-outline">Terug</a>
            </div>
        @endif
    </div>
</section>
@endsection
