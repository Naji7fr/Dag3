@extends('layouts.app')

@section('title', 'Overzicht Behandelingen')

@section('content')
<div class="container">
    <div class="breadcrumb">
        <span><a href="{{ route('home') }}" style="color: #d40935;">Home</a></span> / <span style="color: #3b82f6;">Behandelingen</span>
    </div>

    <h1 style="color: #d40935;">Overzicht behandelingen</h1>

    <section class="filter-card" style="background: white; padding: 12px 20px; border-radius: 8px; margin: 20px 0;">
        <form method="get" action="{{ route('behandelingen.index') }}" style="display: flex; justify-content: flex-end; align-items: center; gap: 15px;">
            <div class="form-group" style="margin: 0; max-width: 250px;">
                <label style="display: block; margin-bottom: 4px; font-size: 12px; font-weight: 500; color: #666;">Behandeling selecteren</label>
                <select name="behandeling" class="behandeling-select" style="width: 100%; padding: 6px 10px; border: 1px solid #ddd; border-radius: 4px; background: white; font-size: 12px;">
                    <option value="">Alle behandelingen</option>
                    @foreach($allBehandelingen as $b)
                        <option value="{{ $b->Naam }}" @if($selectedBehandeling === $b->Naam) selected @endif>
                            {{ $b->Naam }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 8px; flex-shrink: 0;">
                <button type="submit" class="btn btn-primary" style="padding: 6px 18px; font-size: 12px;">Maak selectie</button>
                <a href="{{ route('behandelingen.index') }}" class="btn btn-secondary" style="padding: 6px 18px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center;">Reset</a>
            </div>
        </form>
    </section>

    @if($behandelingen->count() > 0)
        <section class="table-card" style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p class="found" style="color: #999; font-size: 13px; margin: 0 0 15px 0;">
                Gevonden behandelingen - {{ $behandelingen->total() }} behandeling(en)
            </p>

            <!-- Pagination Top -->
            @if($behandelingen->lastPage() > 1)
                <div class="pagination" style="display: flex; gap: 8px; margin-bottom: 15px; justify-content: center;">
                    @if($behandelingen->currentPage() > 1)
                        <a href="{{ $behandelingen->appends(request()->query())->url(1) }}" class="page-link" style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #666; font-size: 13px;">‹</a>
                    @endif
                    @for($i = 1; $i <= $behandelingen->lastPage(); $i++)
                        @if($i == $behandelingen->currentPage())
                            <a href="#" class="page-link active" style="padding: 6px 12px; border: 1px solid #d40935; background: #d40935; color: white; border-radius: 4px; text-decoration: none; font-size: 13px;">{{ $i }}</a>
                        @else
                            <a href="{{ $behandelingen->appends(request()->query())->url($i) }}" class="page-link" style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #666; font-size: 13px;">{{ $i }}</a>
                        @endif
                    @endfor
                    @if($behandelingen->currentPage() < $behandelingen->lastPage())
                        <a href="{{ $behandelingen->appends(request()->query())->url($behandelingen->lastPage()) }}" class="page-link" style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #666; font-size: 13px;">›</a>
                    @endif
                </div>
            @endif

            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #d40935; color: white;">
                        <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 13px;">Soort</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 13px;">Omschrijving</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 13px;">Duur</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 13px;">Prijs</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 13px;">Aantal producten</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 13px;">Actie</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($behandelingen as $behandeling)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px; font-size: 13px;">{{ $behandeling->Naam }}</td>
                            <td style="padding: 12px; font-size: 13px;">{{ $behandeling->Omschrijving }}</td>
                            <td style="padding: 12px; font-size: 13px;">{{ $behandeling->Duurminuten }} min</td>
                            <td style="padding: 12px; font-size: 13px;">EUR {{ number_format($behandeling->Prijs, 2, ',', '.') }}</td>
                            <td style="padding: 12px; font-size: 13px;">{{ $behandeling->aantal_producten }}</td>
                            <td style="padding: 12px; font-size: 13px;">
                                <a href="{{ route('behandelingen.show', $behandeling->Id) }}" class="product-btn" style="color: #3b82f6; text-decoration: none; border: 1px solid #3b82f6; padding: 6px 14px; border-radius: 4px; display: inline-block; font-size: 12px;">
                                    Producten
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @else
        <section class="table-card" style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p class="found" style="text-align: center; color: #9ca3af; font-size: 13px;">
                Er zijn geen behandelingen bekend met deze naam
            </p>
        </section>
    @endif
</div>
@endsection
