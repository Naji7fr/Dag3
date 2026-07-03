@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="breadcrumbs">
    <a href="{{ route('home') }}">Home</a> / <a href="{{ route('klanten.index') }}">Klanten</a> / Detail
</div>

<h1 class="page-title">Klantdetail {{ \App\Services\KlantFormatter::formatNaam($klant) }}</h1>

<div class="card detail-card">
    <div class="detail-row">
        <div class="detail-label">Naam</div>
        <div>{{ \App\Services\KlantFormatter::formatNaam($klant) }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Relatienummer</div>
        <div>{{ $klant['Relatienummer'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Contact e-mail</div>
        <div>{{ $klant['ContactEmail'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Account e-mail</div>
        <div>{{ $klant['AccountEmail'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Straatnaam</div>
        <div>{{ $klant['Straatnaam'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Huisnummer</div>
        <div>{{ $klant['Huisnummer'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Toevoeging</div>
        <div>{{ $klant['Toevoeging'] ?: '-' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Postcode</div>
        <div>{{ $klant['Postcode'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Plaats</div>
        <div>{{ $klant['Plaats'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Mobiel</div>
        <div>{{ $klant['Mobiel'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Bijzonderheden</div>
        <div>{{ $klant['Bijzonderheden'] ?: '-' }}</div>
    </div>

    <div class="detail-actions">
        <a class="btn btn-primary" href="{{ route('klanten.edit', $klant['Id']) }}">Wijzigen</a>
        <a class="btn btn-outline" href="{{ route('klanten.index') }}">Terug</a>
    </div>
</div>
@endsection
