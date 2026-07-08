@extends('layouts.app')

@section('title', 'Prijs wijzigen - ' . $product->Naam)

@section('content')
<section class="product-edit-overzicht">
    <div class="breadcrumbs">
        <a href="{{ route('home') }}">Home</a> /
        <a href="{{ route('behandelingen.index') }}">Behandelingen</a> /
        Wijzigen
    </div>

    <h1 class="product-edit-title">
        Product wijzigen
        <span>{{ $product->Naam }}</span>
    </h1>

    @if ($errors->any())
        <div class="alert alert-error product-edit-error-banner">
            Gegevens niet bijgewerkt
        </div>
    @endif

    <div class="card product-edit-card">
        <form method="post" action="{{ route('behandelingen.update-product', $product->Id) }}" class="product-edit-form">
            @csrf
            @method('PUT')

            <div class="product-edit-grid">
                <div class="product-edit-field">
                    <label>Product</label>
                    <input type="text" value="{{ $product->Naam }}" readonly>
                </div>
                <div class="product-edit-field">
                    <label>Merk</label>
                    <input type="text" value="{{ $product->Merk ?? '-' }}" readonly>
                </div>

                <div class="product-edit-field">
                    <label>Omschrijving</label>
                    <input type="text" value="{{ $product->Omschrijving ?? '-' }}" readonly>
                </div>
                <div class="product-edit-field">
                    <label>EAN-code</label>
                    <input type="text" value="{{ $product->EANcode ?? '-' }}" readonly>
                </div>

                <div class="product-edit-field">
                    <label>Houdbaarheidsdatum</label>
                    <input type="text" value="{{ $product->Houdbaarheidsdatum ? \Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('d-m-Y') : '-' }}" readonly>
                </div>
                <div class="product-edit-field">
                    <label>Aantal op voorraad</label>
                    <input type="text" value="{{ $product->AantalOpVoorraad ?? 0 }}" readonly>
                </div>

                <div class="product-edit-field">
                    <label>Inkoopprijs</label>
                    <input type="text" value="EUR {{ number_format($product->InkoopPrijs, 2, ',', '.') }}" readonly>
                </div>
                <div class="product-edit-field">
                    <label>Leverancier</label>
                    <input type="text" value="{{ $leverancier->Naam ?? '-' }}" readonly>
                </div>

                <div class="product-edit-field">
                    <label>Huidige verkoopprijs</label>
                    <input type="text" value="EUR {{ number_format($product->VerkoopPrijs, 2, ',', '.') }}" readonly>
                </div>
                <div class="product-edit-field">
                    <label>Plaats leverancier</label>
                    <input type="text" value="{{ $leverancier->Plaats ?? '-' }}" readonly>
                </div>

                <div class="product-edit-field">
                    <label for="verkoopprijs">Nieuwe verkoopprijs <span class="required">*</span></label>
                    <input
                        type="text"
                        id="verkoopprijs"
                        name="verkoopprijs"
                        value="{{ old('verkoopprijs', number_format($product->VerkoopPrijs, 2, '.', '')) }}"
                        required
                        class="@error('verkoopprijs') input-error @enderror"
                    >
                    @error('verkoopprijs')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                    <small>Minimaal 30 procent boven de inkoopprijs.</small>
                </div>
                <div class="product-edit-field">
                    <label>Opmerking</label>
                    <input type="text" value="{{ $product->Opmerking ?? '-' }}" readonly>
                </div>
            </div>

            <p class="form-required-note">Velden met een <span>*</span> zijn verplicht.</p>

            <div class="product-edit-actions">
                <button type="submit" class="btn btn-primary">Opslaan</button>
                <a href="{{ route('behandelingen.product-detail', $product->Id) }}" class="btn btn-secondary">Terug</a>
            </div>
        </form>
    </div>
</section>
@endsection
