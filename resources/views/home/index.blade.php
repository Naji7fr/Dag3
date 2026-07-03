@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="dashboard-wrap">
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="dashboard-card">
        <span class="badge">Kapsalon applicatie</span>
        <h1 class="dashboard-title">Eigenaar</h1>
        <h2 class="dashboard-subtitle">Home</h2>
        <p class="dashboard-intro">
            Welkom bij Kniploket Tiko - hier regel je eenvoudig klanten, afspraken en planning voor de salon.
        </p>

        <div class="module-grid">
            <div class="module-card">
                <h3>Accounts</h3>
                <p>Beheer gebruikersaccounts en roltoewijzingen.</p>
                <a class="btn btn-outline" href="#">Openen</a>
            </div>
            <div class="module-card">
                <h3>Medewerkers</h3>
                <p>Overzicht van medewerkers en hun basisgegevens.</p>
                <a class="btn btn-outline" href="#">Openen</a>
            </div>
            <div class="module-card">
                <h3>Beschikbaarheid</h3>
                <p>Bekijk de beschikbaarheid van medewerkers per dag en tijd.</p>
                <a class="btn btn-outline" href="#">Openen</a>
            </div>
            <div class="module-card">
                <h3>Klanten</h3>
                <p>Bekijk en filter klantgegevens op postcode en contactinformatie.</p>
                @if(auth()->user()->isEigenaar())
                    <a class="btn btn-outline" href="{{ route('klanten.index') }}">Openen</a>
                @else
                    <span class="module-disabled">Alleen voor eigenaar</span>
                @endif
            </div>
            <div class="module-card">
                <h3>Afspraken</h3>
                <p>Plan, bekijk en beheer afspraken met status en tijd.</p>
                <a class="btn btn-outline" href="#">Openen</a>
            </div>
            <div class="module-card">
                <h3>Behandelingen</h3>
                <p>Overzicht van behandelingen, duur en prijsinformatie.</p>
                <a class="btn btn-outline" href="#">Openen</a>
            </div>
            <div class="module-card">
                <h3>Producten</h3>
                <p>Bekijk en beheer producten binnen het assortiment.</p>
                <a class="btn btn-outline" href="#">Openen</a>
            </div>
            <div class="module-card">
                <h3>Bestellingen</h3>
                <p>Bekijk en beheer klantbestellingen en bestelstatus.</p>
                @if(auth()->user()->isEigenaar())
                    <a class="btn btn-outline" href="{{ route('bestellingen.index') }}">Openen</a>
                @else
                    <span class="module-disabled">Alleen voor eigenaar</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
