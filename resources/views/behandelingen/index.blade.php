@extends('layouts.app')

@section('title', 'Overzicht Behandelingen')

@section('content')
<div class="container">
    <div class="breadcrumb">
        <span><a href="{{ route('home') }}" style="color: #d40935;">Home</a></span> / <span style="color: #3b82f6;">Behandelingen</span>
    </div>

    <h1>Overzicht behandelingen</h1>

    <section class="filter-card">
        <form method="get" action="{{ route('behandelingen.index') }}" class="filter-box">
            <div class="form-group">
                <label>Behandeling selecteren</label>
                <select name="behandeling" class="behandeling-select">
                    <option value="">Alle behandelingen</option>
                    @foreach($allBehandelingen as $b)
                        <option value="{{ $b->Naam }}" @if($selectedBehandeling === $b->Naam) selected @endif>
                            {{ $b->Naam }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Maak selectie</button>
            <a href="{{ route('behandelingen.index') }}" class="btn btn-secondary">Reset</a>
        </form>
    </section>

    @if($behandelingen->count() > 0)
        <section class="table-card">
            <p class="found">
                Gevonden behandelingen - {{ $behandelingen->total() }} behandeling(en)
            </p>

            <!-- Pagination Top -->
            @if($behandelingen->lastPage() > 1)
                <div class="pagination">
                    @if($behandelingen->currentPage() > 1)
                        <a href="{{ $behandelingen->appends(request()->query())->url(1) }}" class="page-link">‹</a>
                    @endif
                    @for($i = 1; $i <= $behandelingen->lastPage(); $i++)
                        @if($i == $behandelingen->currentPage())
                            <a href="#" class="page-link active">{{ $i }}</a>
                        @else
                            <a href="{{ $behandelingen->appends(request()->query())->url($i) }}" class="page-link">{{ $i }}</a>
                        @endif
                    @endfor
                    @if($behandelingen->currentPage() < $behandelingen->lastPage())
                        <a href="{{ $behandelingen->appends(request()->query())->url($behandelingen->lastPage()) }}" class="page-link">›</a>
                    @endif
                </div>
            @endif

            <table>
                <thead>
                    <tr>
                        <th>Soort</th>
                        <th>Omschrijving</th>
                        <th>Duur</th>
                        <th>Prijs</th>
                        <th>Aantal producten</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($behandelingen as $behandeling)
                        <tr>
                            <td>{{ $behandeling->Naam }}</td>
                            <td>{{ $behandeling->Omschrijving }}</td>
                            <td>{{ $behandeling->Duurminuten }} min</td>
                            <td>EUR {{ number_format($behandeling->Prijs, 2, ',', '.') }}</td>
                            <td>{{ $behandeling->aantal_producten }}</td>
                            <td>
                                <a href="{{ route('behandelingen.show', $behandeling->Id) }}" class="product-btn">
                                    Producten
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @else
        <section class="table-card">
            <p class="found" style="text-align: center; color: #9ca3af;">
                Er zijn geen behandelingen bekend met deze naam
            </p>
        </section>
    @endif
</div>
@endsection
