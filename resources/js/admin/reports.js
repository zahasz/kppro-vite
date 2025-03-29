/**
 * Admin Reports Module - raporty i statystyki przychodów w panelu administratora
 */

const reportsModule = {
    /**
     * Inicjalizacja modułu
     */
    init() {
        console.log('Admin reports module loaded');
        this.setupDateRangePicker();
        this.setupCharts();
        this.setupExportTools();
    },

    /**
     * Konfiguracja selektora zakresu dat
     */
    setupDateRangePicker() {
        const dateRangePicker = document.querySelector('[data-date-range]');
        if (!dateRangePicker) return;

        console.log('Initializing date range picker');
        
        // Obsługa predefiniowanych zakresów dat
        document.querySelectorAll('[data-range-preset]').forEach(button => {
            button.addEventListener('click', function() {
                const preset = this.getAttribute('data-range-preset');
                
                // Usuń aktywną klasę ze wszystkich przycisków
                document.querySelectorAll('[data-range-preset]').forEach(btn => {
                    btn.classList.remove('bg-steel-blue-600', 'text-white');
                    btn.classList.add('bg-white', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
                });
                
                // Dodaj aktywną klasę do klikniętego przycisku
                this.classList.remove('bg-white', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
                this.classList.add('bg-steel-blue-600', 'text-white');
                
                // Oblicz daty na podstawie wybranego zakresu
                const now = new Date();
                let startDate, endDate;
                
                switch (preset) {
                    case 'today':
                        startDate = now;
                        endDate = now;
                        break;
                    case 'week':
                        startDate = new Date(now);
                        startDate.setDate(now.getDate() - 7);
                        endDate = now;
                        break;
                    case 'month':
                        startDate = new Date(now);
                        startDate.setMonth(now.getMonth() - 1);
                        endDate = now;
                        break;
                    case 'quarter':
                        startDate = new Date(now);
                        startDate.setMonth(now.getMonth() - 3);
                        endDate = now;
                        break;
                    case 'year':
                        startDate = new Date(now);
                        startDate.setFullYear(now.getFullYear() - 1);
                        endDate = now;
                        break;
                }
                
                // Formatuj daty (YYYY-MM-DD)
                const formatDate = (date) => {
                    const y = date.getFullYear();
                    const m = (date.getMonth() + 1).toString().padStart(2, '0');
                    const d = date.getDate().toString().padStart(2, '0');
                    return `${y}-${m}-${d}`;
                };
                
                // Ustaw wartości w polach input
                if (startDate && endDate) {
                    document.querySelector('[data-start-date]').value = formatDate(startDate);
                    document.querySelector('[data-end-date]').value = formatDate(endDate);
                    
                    // Wyzwól zdarzenie zmiany, aby zaktualizować raporty
                    this.dispatchEvent(new Event('dateRangeChanged', { bubbles: true }));
                }
                
                console.log(`Selected date range preset: ${preset}`);
            });
        });
        
        // Obsługa ręcznej zmiany dat
        const startDateInput = document.querySelector('[data-start-date]');
        const endDateInput = document.querySelector('[data-end-date]');
        
        if (startDateInput && endDateInput) {
            const handleDateChange = () => {
                const startDate = startDateInput.value;
                const endDate = endDateInput.value;
                
                if (startDate && endDate) {
                    // Usuń aktywną klasę z predefiniowanych zakresów
                    document.querySelectorAll('[data-range-preset]').forEach(btn => {
                        btn.classList.remove('bg-steel-blue-600', 'text-white');
                        btn.classList.add('bg-white', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
                    });
                    
                    console.log(`Custom date range: ${startDate} to ${endDate}`);
                    document.dispatchEvent(new Event('dateRangeChanged', { bubbles: true }));
                }
            };
            
            startDateInput.addEventListener('change', handleDateChange);
            endDateInput.addEventListener('change', handleDateChange);
        }
        
        // Nasłuchuj zmiany zakresu dat i aktualizuj raporty
        document.addEventListener('dateRangeChanged', () => {
            this.updateReports();
        });
    },

    /**
     * Aktualizacja raportów na podstawie wybranego zakresu dat
     */
    updateReports() {
        console.log('Updating reports with new date range');
        
        const startDate = document.querySelector('[data-start-date]')?.value;
        const endDate = document.querySelector('[data-end-date]')?.value;
        
        if (!startDate || !endDate) return;
        
        // Pokaż wskaźnik ładowania
        document.querySelectorAll('[data-loading]').forEach(el => {
            el.classList.remove('hidden');
        });
        
        // Symulacja ładowania danych (w rzeczywistej implementacji byłoby zapytanie AJAX)
        setTimeout(() => {
            // Ukryj wskaźnik ładowania
            document.querySelectorAll('[data-loading]').forEach(el => {
                el.classList.add('hidden');
            });
            
            // Aktualizuj karty z podsumowaniem
            document.querySelectorAll('[data-summary-card]').forEach(card => {
                const randomValue = Math.floor(Math.random() * 10000);
                const valueElement = card.querySelector('[data-value]');
                if (valueElement) {
                    valueElement.textContent = randomValue.toLocaleString('pl-PL') + ' zł';
                }
                
                // Losowy procent wzrostu/spadku
                const change = (Math.random() * 20 - 10).toFixed(1);
                const changeElement = card.querySelector('[data-change]');
                if (changeElement) {
                    changeElement.textContent = `${change}%`;
                    
                    if (parseFloat(change) >= 0) {
                        changeElement.classList.remove('text-red-500');
                        changeElement.classList.add('text-green-500');
                    } else {
                        changeElement.classList.remove('text-green-500');
                        changeElement.classList.add('text-red-500');
                    }
                }
            });
            
            // Aktualizuj wykresy
            this.updateCharts();
            
            console.log('Reports updated successfully');
        }, 800);
    },

    /**
     * Konfiguracja wykresów
     */
    setupCharts() {
        const revenueChart = document.querySelector('[data-revenue-chart]');
        if (!revenueChart) return;
        
        console.log('Initializing revenue charts');
        
        // Przykład inicjalizacji wykresów - w rzeczywistej implementacji użylibyśmy Chart.js
        document.querySelectorAll('[data-chart-type]').forEach(button => {
            button.addEventListener('click', function() {
                const chartType = this.getAttribute('data-chart-type');
                
                // Usuń aktywną klasę ze wszystkich przycisków
                document.querySelectorAll('[data-chart-type]').forEach(btn => {
                    btn.classList.remove('bg-gray-200', 'dark:bg-gray-700');
                    btn.classList.add('bg-white', 'dark:bg-gray-800');
                });
                
                // Dodaj aktywną klasę do klikniętego przycisku
                this.classList.remove('bg-white', 'dark:bg-gray-800');
                this.classList.add('bg-gray-200', 'dark:bg-gray-700');
                
                console.log(`Changed chart type to: ${chartType}`);
                // Wywołalibyśmy tutaj funkcję zmieniającą typ wykresu
            });
        });
    },

    /**
     * Aktualizacja wykresów
     */
    updateCharts() {
        // Kod do aktualizacji danych w wykresach
        console.log('Updating chart data');
    },

    /**
     * Konfiguracja narzędzi do eksportu raportów
     */
    setupExportTools() {
        document.querySelectorAll('[data-export]').forEach(button => {
            button.addEventListener('click', function() {
                const format = this.getAttribute('data-export');
                const startDate = document.querySelector('[data-start-date]')?.value;
                const endDate = document.querySelector('[data-end-date]')?.value;
                
                if (!startDate || !endDate) {
                    alert('Wybierz zakres dat przed eksportem raportu.');
                    return;
                }
                
                console.log(`Exporting report in ${format} format for period ${startDate} to ${endDate}`);
                
                // Wyświetl komunikat o rozpoczęciu eksportu
                this.disabled = true;
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="spinner inline-block w-4 h-4 border-2 border-t-transparent border-white rounded-full animate-spin mr-2"></span> Eksportowanie...';
                
                // Symulacja eksportu (w rzeczywistej implementacji byłoby zapytanie AJAX)
                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = originalText;
                    
                    alert(`Raport w formacie ${format.toUpperCase()} został pomyślnie wyeksportowany.`);
                }, 1500);
            });
        });
    }
};

// Eksportujemy moduł
export default reportsModule;

// Automatycznie inicjalizujemy moduł przy imporcie
document.addEventListener('DOMContentLoaded', () => {
    reportsModule.init();
}); 