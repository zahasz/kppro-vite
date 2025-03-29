/**
 * Dashboard module - odpowiada za funkcje dostępne w panelu użytkownika
 */

// Funkcje dla dashboardu
const dashboardModule = {
    /**
     * Inicjalizacja modułu dashboard
     */
    init() {
        console.log('Dashboard module loaded');
        this.setupCharts();
        this.setupWidgets();
        this.setupRefreshHandlers();
    },

    /**
     * Konfiguracja wykresów na dashboardzie
     */
    setupCharts() {
        const chartElements = document.querySelectorAll('[data-chart]');
        if (chartElements.length === 0) return;

        console.log('Initializing dashboard charts');
        // Tutaj kod do inicjalizacji wykresów - np. z Chart.js
        // To jest tylko przykład, który będzie rozbudowany gdy dodamy bibliotekę wykresów
    },

    /**
     * Konfiguracja widgetów na dashboardzie
     */
    setupWidgets() {
        const widgets = document.querySelectorAll('[data-widget]');
        if (widgets.length === 0) return;

        console.log('Initializing dashboard widgets');
        
        // Obsługa widgetów z możliwością zwijania
        document.querySelectorAll('[data-widget-toggle]').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-widget-toggle');
                const target = document.getElementById(targetId);
                if (target) {
                    target.classList.toggle('hidden');
                    
                    // Zmiana ikony rozwijania/zwijania
                    const icon = this.querySelector('svg');
                    if (icon) {
                        icon.classList.toggle('rotate-180');
                    }
                }
            });
        });
    },

    /**
     * Obsługa odświeżania danych na dashboardzie
     */
    setupRefreshHandlers() {
        document.querySelectorAll('[data-refresh]').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-refresh');
                const target = document.getElementById(targetId);
                
                if (target) {
                    // Dodanie klasy ładowania
                    target.classList.add('opacity-50');
                    
                    // Symulacja ładowania danych (w rzeczywistej aplikacji byłoby zapytanie do API)
                    setTimeout(() => {
                        target.classList.remove('opacity-50');
                        console.log(`Refreshed widget: ${targetId}`);
                    }, 800);
                }
            });
        });
    }
};

// Eksportujemy moduł
export default dashboardModule;

// Automatycznie inicjalizujemy moduł przy imporcie
document.addEventListener('DOMContentLoaded', () => {
    dashboardModule.init();
}); 