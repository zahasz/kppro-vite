@extends('layouts.app')

@section('title', 'Panel główny')

@section('header', 'Panel główny')

@section('content')
<div class="row">
    <!-- Sekcja statystyk -->
    <div class="col-12">
        <div class="stats-section">
            <h3 class="section-title">Statystyki finansowe</h3>
            <div class="row p-3">
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-value">25 000 zł</div>
                        <div class="stat-label">Przychody (bieżący miesiąc)</div>
                        <span class="stat-count">15 transakcji</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-value">15 000 zł</div>
                        <div class="stat-label">Koszty (bieżący miesiąc)</div>
                        <span class="stat-count">8 transakcji</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-value">10 000 zł</div>
                        <div class="stat-label">Zysk (bieżący miesiąc)</div>
                        <span class="stat-count">+15% vs poprzedni miesiąc</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-value">5</div>
                        <div class="stat-label">Niezapłacone faktury</div>
                        <span class="stat-count">12 000 zł łącznie</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Moduły systemu -->
    <div class="col-md-4">
        <div class="info-box bg-finances">
            <h3>Finanse</h3>
            <div class="info-box-status">Aktywny</div>
            <div class="info-box-description">
                Zarządzaj finansami firmy, śledź przychody i wydatki, generuj raporty finansowe.
            </div>
            <a href="#" class="info-box-button">
                <i class="fas fa-arrow-right mr-2"></i>Przejdź do modułu
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="info-box bg-accounting">
            <h3>Księgowość</h3>
            <div class="info-box-status">Aktywny</div>
            <div class="info-box-description">
                Prowadź księgowość, zarządzaj fakturami i dokumentami księgowymi.
            </div>
            <a href="#" class="info-box-button">
                <i class="fas fa-arrow-right mr-2"></i>Przejdź do modułu
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="info-box bg-offers">
            <h3>Oferty</h3>
            <div class="info-box-status">Aktywny</div>
            <div class="info-box-description">
                Twórz i zarządzaj ofertami dla klientów, śledź status negocjacji.
            </div>
            <a href="#" class="info-box-button">
                <i class="fas fa-arrow-right mr-2"></i>Przejdź do modułu
            </a>
        </div>
    </div>

    <!-- Szybkie akcje -->
    <div class="col-12">
        <div class="quick-actions">
            <h3 class="section-title">Szybkie akcje</h3>
            <div class="btn-group">
                <button class="btn">
                    <i class="fas fa-plus mr-2"></i>Nowa faktura
                </button>
                <button class="btn">
                    <i class="fas fa-file-invoice mr-2"></i>Nowa oferta
                </button>
                <button class="btn">
                    <i class="fas fa-user-plus mr-2"></i>Nowy kontrahent
                </button>
            </div>
        </div>
    </div>

    <!-- Ostatnie aktywności i alerty -->
    <div class="col-md-8">
        <div class="recent-activities">
            <h3 class="section-title p-3 border-bottom">Ostatnie aktywności</h3>
            <div class="activity-list">
                <div class="activity-item">
                    <span class="activity-text">Utworzono nową fakturę #123</span>
                    <span class="activity-time">2 godziny temu</span>
                </div>
                <div class="activity-item">
                    <span class="activity-text">Dodano nowego kontrahenta: ABC Sp. z o.o.</span>
                    <span class="activity-time">wczoraj</span>
                </div>
                <div class="activity-item">
                    <span class="activity-text">Zaktualizowano ofertę #45</span>
                    <span class="activity-time">2 dni temu</span>
                </div>
                <div class="activity-item">
                    <span class="activity-text">Zaksięgowano płatność od XYZ Sp. z o.o.</span>
                    <span class="activity-time">3 dni temu</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="alerts-section">
            <h3 class="section-title">Alerty i powiadomienia</h3>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                3 faktury oczekują na płatność
            </div>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Nowa aktualizacja systemu dostępna
            </div>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Backup danych wykonany pomyślnie
            </div>
        </div>
    </div>
</div>
@endsection 