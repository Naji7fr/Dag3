@extends('layouts.app')

@section('title', $product->Naam)

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <a href="{{ route('behandelingen.index') }}">Behandelingen</a> / 
    {{ $product->Naam }}
</div>

<h1 style="color: #D32F2F;">Productdetail</h1>
<h2 style="color: #6b7280; font-size: 18px; font-weight: 400; margin: 8px 0 20px;">{{ $product->Naam }}</h2>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 12px 20px;">
        <div style="font-weight: 700; color: #6b7280;">Product</div>
        <div style="color: #1f2937;">{{ $product->Naam }}</div>

        <div style="font-weight: 700; color: #6b7280;">Merk</div>
        <div style="color: #1f2937;">{{ $product->Merk ?? '-' }}</div>

        <div style="font-weight: 700; color: #6b7280;">Omschrijving</div>
        <div style="color: #1f2937;">{{ $product->Omschrijving ?? '-' }}</div>

        <div style="font-weight: 700; color: #6b7280;">EAN-code</div>
        <div style="color: #1f2937;">{{ $product->EANcode ?? '-' }}</div>

        <div style="font-weight: 700; color: #6b7280;">Houdbaarheidsdatum</div>
        <div style="color: #1f2937;">{{ $product->HoudbaarheidsNota ?? '-' }}</div>

        <div style="font-weight: 700; color: #6b7280;">Inkoopprijs</div>
        <div style="color: #1f2937;">EUR {{ number_format($product->InkoopPrijs, 2, ',', '.') }}</div>

        <div style="font-weight: 700; color: #6b7280;">Verkoopprijs</div>
        <div style="color: #1f2937;">EUR {{ number_format($product->VerkoopPrijs, 2, ',', '.') }}</div>

        <div style="font-weight: 700; color: #6b7280;">Aantal op voorraad</div>
        <div style="color: #1f2937;">{{ $product->AantalOpVoorraad ?? 0 }}</div>

        @if($leverancier)
            <div style="font-weight: 700; color: #6b7280;">Leverancier</div>
            <div style="color: #1f2937;">{{ $leverancier->Naam }}</div>

            <div style="font-weight: 700; color: #6b7280;">Postcode leverancier</div>
            <div style="color: #1f2937;">{{ $leverancier->Postcode ?? '-' }}</div>

            <div style="font-weight: 700; color: #6b7280;">Plaats leverancier</div>
            <div style="color: #1f2937;">{{ $leverancier->Plaats ?? '-' }}</div>

            <div style="font-weight: 700; color: #6b7280;">E-mail leverancier</div>
            <div style="color: #1f2937;">{{ $leverancier->Email ?? '-' }}</div>

            <div style="font-weight: 700; color: #6b7280;">Mobiel leverancier</div>
            <div style="color: #1f2937;">{{ $leverancier->Mobiel ?? '-' }}</div>

            <div style="font-weight: 700; color: #6b7280;">Opmerking</div>
            <div style="color: #1f2937;">{{ $product->Opmerking ?? '-' }}</div>
        @endif
    </div>

    <div style="border-top: 1px solid #e5e7eb; margin-top: 20px; padding-top: 20px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="{{ route('behandelingen.edit-product', $product->Id) }}" class="btn btn-primary">
            Wijzigen
        </a>
        <a href="{{ route('behandelingen.index') }}" class="btn btn-secondary">Terug</a>
    </div>
</div>
@endsection
