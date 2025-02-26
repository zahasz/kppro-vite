@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
    <!-- Szybkie akcje -->
    <div class="quick-actions mb-4">
        <h3 class="section-title">Szybkie akcje</h3>
        <button class="btn">
            <i class="fas fa-plus-circle"></i> Nowa faktura
        </button>
        <button class="btn">
            <i class="fas fa-file-invoice"></i> Nowa oferta
        </button>
        <button class="btn">
            <i class="fas fa-user-plus"></i> Nowy pracownik
        </button>
        <button class="btn">
            <i class="fas fa-building"></i> Nowy kontrahent
        </button>
    </div>

    <!-- Statystyki -->
    <div class="stats-section">
        <h3 class="section-title">Statystyki finansowe</h3>
        
        <!-- Przychody -->
        <div class="stats-row">
            <div class="stat-box income">
                <div class="stat-label">Przychody (bieżący miesiąc)</div>
                <div class="stat-value positive">45,250 zł</div>
                <div class="stat-count">
                    <i class="fas fa-arrow-up text-success"></i>
                    12% więcej niż w poprzednim miesiącu
                </div>
            </div>
            <div class="stat-box income">
                <div class="stat-label">Przychody (rok)</div>
                <div class="stat-value positive">524,800 zł</div>
                <div class="stat-count">
                    <i class="fas fa-arrow-up text-success"></i>
                    8% więcej niż w poprzednim roku
                </div>
            </div>
            <div class="stat-box income">
                <div class="stat-label">Należności</div>
                <div class="stat-value">15,750 zł</div>
                <div class="stat-count">
                    <i class="fas fa-file-invoice"></i>
                    8 faktur oczekujących
                </div>
            </div>
        </div>

        <!-- Koszty -->
        <div class="stats-row">
            <div class="stat-box expenses">
                <div class="stat-label">Koszty (bieżący miesiąc)</div>
                <div class="stat-value negative">32,150 zł</div>
                <div class="stat-count">
                    <i class="fas fa-arrow-down text-danger"></i>
                    5% mniej niż w poprzednim miesiącu
                </div>
            </div>
            <div class="stat-box expenses">
                <div class="stat-label">Koszty (rok)</div>
                <div class="stat-value negative">385,600 zł</div>
                <div class="stat-count">
                    <i class="fas fa-arrow-up text-danger"></i>
                    3% więcej niż w poprzednim roku
                </div>
            </div>
            <div class="stat-box expenses">
                <div class="stat-label">Zobowiązania</div>
                <div class="stat-value">12,350 zł</div>
                <div class="stat-count">
                    <i class="fas fa-file-invoice"></i>
                    5 faktur do zapłaty
                </div>
            </div>
        </div>
    </div>

    <!-- Moduły systemu -->
    <div class="row">
        <div class="col-md-4">
            <div class="info-box bg-finances">
                <h3>
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Finanse
                </h3>
                <div class="info-box-status">Aktywny</div>
                <div class="info-box-description">
                    Zarządzaj finansami firmy, śledź przychody i wydatki, generuj raporty finansowe.
                </div>
                <a href="#" class="info-box-button">
                    <i class="fas fa-arrow-right"></i>
                    Przejdź do modułu
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="info-box bg-accounting">
                <h3>
                    <i class="fas fa-book-open mr-2"></i>
                    Księgowość
                </h3>
                <div class="info-box-status">Aktywny</div>
                <div class="info-box-description">
                    Prowadź księgowość, zarządzaj fakturami i dokumentami księgowymi.
                </div>
                <a href="#" class="info-box-button">
                    <i class="fas fa-arrow-right"></i>
                    Przejdź do modułu
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="info-box bg-offers">
                <h3>
                    <i class="fas fa-handshake mr-2"></i>
                    Oferty
                </h3>
                <div class="info-box-status">Aktywny</div>
                <div class="info-box-description">
                    Twórz i zarządzaj ofertami dla klientów, śledź status negocjacji.
                </div>
                <a href="#" class="info-box-button">
                    <i class="fas fa-arrow-right"></i>
                    Przejdź do modułu
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="info-box bg-employees">
                <h3>
                    <i class="fas fa-users mr-2"></i>
                    Pracownicy
                </h3>
                <div class="info-box-status">Aktywny</div>
                <div class="info-box-description">
                    Zarządzaj pracownikami, urlopami, czasem pracy i wynagrodzeniami.
                </div>
                <a href="#" class="info-box-button">
                    <i class="fas fa-arrow-right"></i>
                    Przejdź do modułu
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="info-box bg-warehouse">
                <h3>
                    <i class="fas fa-warehouse mr-2"></i>
                    Magazyn
                </h3>
                <div class="info-box-status">Aktywny</div>
                <div class="info-box-description">
                    Kontroluj stany magazynowe, zarządzaj dostawami i inwentaryzacją.
                </div>
                <a href="#" class="info-box-button">
                    <i class="fas fa-arrow-right"></i>
                    Przejdź do modułu
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="info-box bg-estimates">
                <h3>
                    <i class="fas fa-calculator mr-2"></i>
                    Kosztorysy
                </h3>
                <div class="info-box-status">Aktywny</div>
                <div class="info-box-description">
                    Twórz i zarządzaj kosztorysami, kalkuluj koszty projektów.
                </div>
                <a href="#" class="info-box-button">
                    <i class="fas fa-arrow-right"></i>
                    Przejdź do modułu
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="info-box bg-contractors">
                <h3>
                    <i class="fas fa-building mr-2"></i>
                    Kontrahenci
                </h3>
                <div class="info-box-status">Aktywny</div>
                <div class="info-box-description">
                    Zarządzaj bazą kontrahentów, historią współpracy i umowami.
                </div>
                <a href="#" class="info-box-button">
                    <i class="fas fa-arrow-right"></i>
                    Przejdź do modułu
                </a>
            </div>
        </div>
    </div>

    <!-- Ostatnie aktywności i alerty -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="recent-activities">
                <h3 class="section-title p-3 border-bottom">
                    <i class="fas fa-history mr-2"></i>
                    Ostatnie aktywności
                </h3>
                <div class="activity-list">
                    <div class="activity-item">
                        <span class="activity-text">
                            <i class="fas fa-file-invoice text-blue-500"></i>
                            Utworzono nową fakturę #123
                        </span>
                        <span class="activity-time">2 godziny temu</span>
                    </div>
                    <div class="activity-item">
                        <span class="activity-text">
                            <i class="fas fa-user-plus text-green-500"></i>
                            Dodano nowego kontrahenta: ABC Sp. z o.o.
                        </span>
                        <span class="activity-time">wczoraj</span>
                    </div>
                    <div class="activity-item">
                        <span class="activity-text">
                            <i class="fas fa-edit text-yellow-500"></i>
                            Zaktualizowano ofertę #45
                        </span>
                        <span class="activity-time">2 dni temu</span>
                    </div>
                    <div class="activity-item">
                        <span class="activity-text">
                            <i class="fas fa-money-bill-wave text-green-500"></i>
                            Zaksięgowano płatność od XYZ Sp. z o.o.
                        </span>
                        <span class="activity-time">3 dni temu</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="alerts-section">
                <h3 class="section-title">
                    <i class="fas fa-bell mr-2"></i>
                    Alerty i powiadomienia
                </h3>
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