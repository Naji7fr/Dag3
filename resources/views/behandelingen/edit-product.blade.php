@extends('layouts.app')

@section('title', 'Prijs wijzigen - ' . $product->Naam)

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <a href="{{ route('behandelingen.index') }}">Behandelingen</a> / 
    Detail
</div>

<h1 style="color: #d40935; margin-bottom: 0;">Productdetail</h1>
<h2 style="color: #6b7280; font-size: 18px; font-weight: 400; margin: 8px 0 20px;">{{ $product->Naam }}</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="background: white; border-radius: 16px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
    <form method="post" action="{{ route('behandelingen.update-product', $product->Id) }}">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <strong style="color: #6b7280;">Product</strong>
                <p style="color: #1f2937; margin: 4px 0;">{{ $product->Naam }}</p>
            </div>

            <div>
                <strong style="color: #6b7280;">Merk</strong>
                <p style="color: #1f2937; margin: 4px 0;">{{ $product->Merk ?? '-' }}</p>
            </div>

            <div>
                <strong style="color: #6b7280;">Omschrijving</strong>
                <p style="color: #1f2937; margin: 4px 0;">{{ $product->Omschrijving ?? '-' }}</p>
            </div>

            <div>
                <strong style="color: #6b7280;">EAN-code</strong>
                <p style="color: #1f2937; margin: 4px 0;">{{ $product->EANcode ?? '-' }}</p>
            </div>

            <div>
                <strong style="color: #6b7280;">Houdbaarheidsdatum</strong>
                <p style="color: #1f2937; margin: 4px 0;">{{ $product->HoudbaarheidsNota ?? '-' }}</p>
            </div>

            <div>
                <strong style="color: #6b7280;">Inkoopprijs</strong>
                <p style="color: #1f2937; margin: 4px 0;">EUR {{ number_format($product->InkoopPrijs, 2, ',', '.') }}</p>
            </div>

            <div>
                <strong style="color: #6b7280;">Verkoopprijs</strong>
                <p style="color: #1f2937; margin: 4px 0;">EUR {{ number_format($product->VerkoopPrijs, 2, ',', '.') }}</p>
            </div>

            <div>
                <strong style="color: #6b7280;">Aantal op voorraad</strong>
                <p style="color: #1f2937; margin: 4px 0;">{{ $product->AantalOpVoorraad ?? 0 }}</p>
            </div>

            @if($leverancier)
                <div>
                    <strong style="color: #6b7280;">Leverancier</strong>
                    <p style="color: #1f2937; margin: 4px 0;">{{ $leverancier->Naam }}</p>
                </div>

                <div>
                    <strong style="color: #6b7280;">Postcode leverancier</strong>
                    <p style="color: #1f2937; margin: 4px 0;">{{ $leverancier->Postcode ?? '-' }}</p>
                </div>

                <div>
                    <strong style="color: #6b7280;">Plaats leverancier</strong>
                    <p style="color: #1f2937; margin: 4px 0;">{{ $leverancier->Plaats ?? '-' }}</p>
                </div>

                <div>
                    <strong style="color: #6b7280;">E-mail leverancier</strong>
                    <p style="color: #1f2937; margin: 4px 0;">{{ $leverancier->Email ?? '-' }}</p>
                </div>

                <div>
                    <strong style="color: #6b7280;">Mobiel leverancier</strong>
                    <p style="color: #1f2937; margin: 4px 0;">{{ $leverancier->Mobiel ?? '-' }}</p>
                </div>

                <div>
                    <strong style="color: #6b7280;">Opmerking</strong>
                    <p style="color: #1f2937; margin: 4px 0;">{{ $product->Opmerking ?? '-' }}</p>
                </div>
            @endif
        </div>

        <div style="border-top: 1px solid #e5e7eb; margin-top: 20px; padding-top: 20px;">
            <div style="margin-bottom: 16px;">
                <label for="verkoopprijs" style="display: block; font-weight: 700; margin-bottom: 6px; color: #6b7280;">Nieuwe verkoopprijs *</label>
                <input 
                    type="text" 
                    id="verkoopprijs" 
                    name="verkoopprijs" 
                    value="{{ old('verkoopprijs', $product->VerkoopPrijs) }}"
                    placeholder="EUR 0,00"
                    required
                    style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 14px;"
                    class="@error('verkoopprijs') is-invalid @enderror"
                >
                @error('verkoopprijs')
                    <span style="color: #d40935; font-size: 12px; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <p style="font-size: 12px; color: #6b7280; margin-bottom: 16px;">
                <strong>Minimumprijs (30% markering):</strong> EUR {{ number_format($product->InkoopPrijs * 1.30, 2, ',', '.') }}
            </p>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary">Wijzigen</button>
            <a href="{{ route('behandelingen.product-detail', $product->Id) }}" class="btn btn-secondary">Terug</a>
        </div>
    </form>
</div>
@endsection
