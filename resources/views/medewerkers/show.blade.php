@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="breadcrumbs">
    <a href="{{ route('home') }}">Home</a> / <a href="{{ route('medewerkers.index') }}">Medewerkers</a> / Detail
</div>

<h1 class="page-title">
    @if($medewerker['Tussenvoegsel'])
        Medewerkerdetail {{ $medewerker['Voornaam'] }} {{ $medewerker['Tussenvoegsel'] }} {{ $medewerker['Achternaam'] }}
    @else
        Medewerkerdetail {{ $medewerker['Voornaam'] }} {{ $medewerker['Achternaam'] }}
    @endif
</h1>

<div class="card detail-card">
    <div class="detail-row">
        <div class="detail-label">Naam</div>
        <div>
            @if($medewerker['Tussenvoegsel'])
                {{ $medewerker['Voornaam'] }} {{ $medewerker['Tussenvoegsel'] }} {{ $medewerker['Achternaam'] }}
            @else
                {{ $medewerker['Voornaam'] }} {{ $medewerker['Achternaam'] }}
            @endif
        </div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Specialisatie</div>
        <div>{{ $medewerker['Specialisatie'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Geboortedatum</div>
        <div>
            @php
                $geboortedatum = \DateTime::createFromFormat('Y-m-d', $medewerker['Geboortedatum']);
                echo $geboortedatum ? $geboortedatum->format('d-m-Y') : '-';
            @endphp
        </div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Contact e-mail</div>
        <div>{{ $medewerker['ContactEmail'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Account e-mail</div>
        <div>{{ $medewerker['AccountEmail'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Straatnaam</div>
        <div>{{ $medewerker['Straatnaam'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Huisnummer</div>
        <div>{{ $medewerker['Huisnummer'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Toevoeging</div>
        <div>{{ $medewerker['Toevoeging'] ?: '-' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Postcode</div>
        <div>{{ $medewerker['Postcode'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Plaats</div>
        <div>{{ $medewerker['Plaats'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Mobiel</div>
        <div>{{ $medewerker['Mobiel'] }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Opmerking</div>
        <div>{{ $medewerker['Opmerking'] ?: '-' }}</div>
    </div>

    <div class="detail-actions">
        <a class="btn btn-primary" href="{{ route('medewerkers.edit', $medewerker['Id']) }}">Wijzigen</a>
        <a class="btn btn-outline" href="{{ route('medewerkers.index') }}">Terug</a>
    </div>
</div>
@endsection
