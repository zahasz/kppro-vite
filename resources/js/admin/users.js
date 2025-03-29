/**
 * Admin Users Module - zarządzanie użytkownikami w panelu administratora
 */

const usersModule = {
    /**
     * Inicjalizacja modułu
     */
    init() {
        console.log('Admin users module loaded');
        this.setupUserTable();
        this.setupFilters();
        this.setupForms();
    },

    /**
     * Konfiguracja tabeli użytkowników
     */
    setupUserTable() {
        const userTable = document.querySelector('[data-users-table]');
        if (!userTable) return;

        console.log('Initializing users table');
        
        // Sortowanie kolumn
        document.querySelectorAll('[data-sort]').forEach(header => {
            header.addEventListener('click', function() {
                const column = this.getAttribute('data-sort');
                const currentOrder = this.getAttribute('data-order') || 'asc';
                const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
                
                // Aktualizacja strzałek sortowania
                document.querySelectorAll('[data-sort]').forEach(h => {
                    h.removeAttribute('data-order');
                    h.querySelector('.sort-icon')?.classList.remove('rotate-180');
                });
                
                this.setAttribute('data-order', newOrder);
                if (newOrder === 'desc') {
                    this.querySelector('.sort-icon')?.classList.add('rotate-180');
                }
                
                console.log(`Sorting users by ${column} in ${newOrder} order`);
                // Tutaj kod do sortowania tabeli (w rzeczywistej implementacji)
            });
        });
        
        // Obsługa akcji dla użytkowników
        document.querySelectorAll('[data-user-action]').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-user-action');
                const userId = this.closest('tr').getAttribute('data-user-id');
                
                console.log(`Action ${action} for user ${userId}`);
                
                // Przykładowe akcje
                switch (action) {
                    case 'edit':
                        // Kod do edycji użytkownika
                        break;
                    case 'delete':
                        if (confirm('Czy na pewno chcesz usunąć tego użytkownika?')) {
                            console.log(`Deleting user ${userId}`);
                            // Kod do usunięcia użytkownika
                        }
                        break;
                    case 'reset-password':
                        if (confirm('Czy na pewno chcesz zresetować hasło tego użytkownika?')) {
                            console.log(`Resetting password for user ${userId}`);
                            // Kod do resetowania hasła
                        }
                        break;
                }
            });
        });
    },

    /**
     * Konfiguracja filtrów dla listy użytkowników
     */
    setupFilters() {
        const filterForm = document.querySelector('[data-users-filter]');
        if (!filterForm) return;
        
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Filtering users');
            
            // Kod do filtrowania użytkowników
            const formData = new FormData(this);
            const params = new URLSearchParams();
            
            for (const [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }
            
            // W rzeczywistej implementacji ten kod przekierowałby do nowego URL z parametrami
            console.log(`Filter parameters: ${params.toString()}`);
        });
        
        // Przycisk czyszczenia filtrów
        const resetButton = document.querySelector('[data-filter-reset]');
        if (resetButton) {
            resetButton.addEventListener('click', function() {
                filterForm.reset();
                filterForm.dispatchEvent(new Event('submit'));
            });
        }
    },

    /**
     * Konfiguracja formularzy
     */
    setupForms() {
        const userForm = document.querySelector('[data-user-form]');
        if (!userForm) return;
        
        userForm.addEventListener('submit', function(e) {
            // Walidacja po stronie klienta
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    // Dodanie klasy błędu
                    field.classList.add('border-red-500');
                    
                    // Dodanie komunikatu o błędzie
                    const errorEl = field.nextElementSibling;
                    if (errorEl && errorEl.classList.contains('error-message')) {
                        errorEl.textContent = 'To pole jest wymagane';
                        errorEl.classList.remove('hidden');
                    }
                } else {
                    field.classList.remove('border-red-500');
                    
                    const errorEl = field.nextElementSibling;
                    if (errorEl && errorEl.classList.contains('error-message')) {
                        errorEl.classList.add('hidden');
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                console.log('Form validation failed');
            } else {
                console.log('Form is valid, submitting');
            }
        });
    }
};

// Eksportujemy moduł
export default usersModule;

// Automatycznie inicjalizujemy moduł przy imporcie
document.addEventListener('DOMContentLoaded', () => {
    usersModule.init();
}); 