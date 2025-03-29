/**
 * Admin Subscriptions Module - zarządzanie subskrypcjami w panelu administratora
 */

const subscriptionsModule = {
    /**
     * Inicjalizacja modułu
     */
    init() {
        console.log('Admin subscriptions module loaded');
        this.setupSubscriptionTable();
        this.setupPlanEditors();
        this.setupCharts();
    },

    /**
     * Konfiguracja tabeli subskrypcji
     */
    setupSubscriptionTable() {
        const subTable = document.querySelector('[data-subscriptions-table]');
        if (!subTable) return;

        console.log('Initializing subscriptions table');
        
        // Filtrowanie statusów subskrypcji
        document.querySelectorAll('[data-status-filter]').forEach(filter => {
            filter.addEventListener('click', function() {
                const status = this.getAttribute('data-status-filter');
                
                // Usuń aktywną klasę ze wszystkich filtrów
                document.querySelectorAll('[data-status-filter]').forEach(f => {
                    f.classList.remove('bg-steel-blue-100', 'dark:bg-steel-blue-900', 'text-steel-blue-800', 'dark:text-steel-blue-200');
                    f.classList.add('bg-gray-100', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
                });
                
                // Dodaj aktywną klasę do klikniętego filtra
                this.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
                this.classList.add('bg-steel-blue-100', 'dark:bg-steel-blue-900', 'text-steel-blue-800', 'dark:text-steel-blue-200');
                
                // Pokaż/ukryj wiersze tabeli w zależności od statusu
                const rows = subTable.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    if (status === 'all' || status === rowStatus) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
                
                console.log(`Filtering subscriptions by status: ${status}`);
            });
        });
        
        // Obsługa akcji dla subskrypcji
        document.querySelectorAll('[data-subscription-action]').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-subscription-action');
                const subId = this.closest('tr').getAttribute('data-subscription-id');
                
                console.log(`Action ${action} for subscription ${subId}`);
                
                // Przykładowe akcje
                switch (action) {
                    case 'view':
                        // Kod do podglądu subskrypcji
                        break;
                    case 'cancel':
                        if (confirm('Czy na pewno chcesz anulować tę subskrypcję?')) {
                            console.log(`Cancelling subscription ${subId}`);
                            // Kod do anulowania subskrypcji
                        }
                        break;
                    case 'extend':
                        const months = prompt('Podaj liczbę miesięcy przedłużenia:', '1');
                        if (months && !isNaN(months)) {
                            console.log(`Extending subscription ${subId} by ${months} months`);
                            // Kod do przedłużenia subskrypcji
                        }
                        break;
                }
            });
        });
    },

    /**
     * Konfiguracja edytorów planów subskrypcji
     */
    setupPlanEditors() {
        const planForm = document.querySelector('[data-plan-form]');
        if (!planForm) return;
        
        console.log('Initializing subscription plan editor');
        
        // Dynamiczne dodawanie funkcji do planu
        const addFeatureBtn = document.querySelector('[data-add-feature]');
        const featuresContainer = document.querySelector('[data-features-container]');
        
        if (addFeatureBtn && featuresContainer) {
            addFeatureBtn.addEventListener('click', function() {
                const featureCount = featuresContainer.querySelectorAll('[data-feature]').length;
                
                const featureRow = document.createElement('div');
                featureRow.setAttribute('data-feature', '');
                featureRow.classList.add('flex', 'items-center', 'gap-2', 'mb-2');
                
                featureRow.innerHTML = `
                    <input type="text" name="features[${featureCount}]" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Funkcja planu">
                    <button type="button" data-remove-feature class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                `;
                
                featuresContainer.appendChild(featureRow);
                
                // Obsługa usuwania funkcji
                featureRow.querySelector('[data-remove-feature]').addEventListener('click', function() {
                    featureRow.remove();
                });
            });
        }
        
        // Obsługa przełącznika ceny miesięcznej/rocznej
        const pricingToggle = document.querySelector('[data-pricing-toggle]');
        if (pricingToggle) {
            pricingToggle.addEventListener('change', function() {
                const monthlyInputs = document.querySelectorAll('[data-pricing="monthly"]');
                const yearlyInputs = document.querySelectorAll('[data-pricing="yearly"]');
                
                if (this.checked) {
                    // Roczne
                    monthlyInputs.forEach(el => el.classList.add('hidden'));
                    yearlyInputs.forEach(el => el.classList.remove('hidden'));
                } else {
                    // Miesięczne
                    monthlyInputs.forEach(el => el.classList.remove('hidden'));
                    yearlyInputs.forEach(el => el.classList.add('hidden'));
                }
            });
        }
        
        // Obsługa formularza
        planForm.addEventListener('submit', function(e) {
            // Walidacja
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                console.log('Form validation failed');
            } else {
                console.log('Form is valid, submitting');
            }
        });
    },

    /**
     * Konfiguracja wykresów statystyk subskrypcji
     */
    setupCharts() {
        const chartContainer = document.querySelector('[data-subscription-chart]');
        if (!chartContainer) return;
        
        console.log('Initializing subscription charts');
        
        // Tutaj kod inicjalizujący wykresy ze statystykami
        // W rzeczywistej implementacji użylibyśmy biblioteki typu Chart.js
    }
};

// Eksportujemy moduł
export default subscriptionsModule;

// Automatycznie inicjalizujemy moduł przy imporcie
document.addEventListener('DOMContentLoaded', () => {
    subscriptionsModule.init();
}); 