/**
 * Admin System Module - zarządzanie systemem w panelu administratora
 */

const systemModule = {
    /**
     * Inicjalizacja modułu
     */
    init() {
        console.log('Admin system module loaded');
        this.setupLogViewer();
        this.setupBackups();
        this.setupSystemInfo();
        this.setupLoginHistory();
    },

    /**
     * Konfiguracja przeglądarki logów
     */
    setupLogViewer() {
        const logViewer = document.querySelector('[data-log-viewer]');
        if (!logViewer) return;
        
        console.log('Initializing log viewer');
        
        // Obsługa przełączników poziomu logów
        document.querySelectorAll('[data-log-level]').forEach(button => {
            button.addEventListener('click', function() {
                const level = this.getAttribute('data-log-level');
                
                // Usuń aktywną klasę ze wszystkich przycisków
                document.querySelectorAll('[data-log-level]').forEach(btn => {
                    btn.classList.remove('bg-steel-blue-600', 'text-white');
                    btn.classList.add('bg-gray-100', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
                });
                
                // Dodaj aktywną klasę do klikniętego przycisku
                this.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
                this.classList.add('bg-steel-blue-600', 'text-white');
                
                // Filtruj logi według poziomu
                const logEntries = document.querySelectorAll('[data-log-entry]');
                
                logEntries.forEach(entry => {
                    const entryLevel = entry.getAttribute('data-log-entry');
                    if (level === 'all' || entryLevel === level) {
                        entry.classList.remove('hidden');
                    } else {
                        entry.classList.add('hidden');
                    }
                });
                
                console.log(`Filtered logs by level: ${level}`);
            });
        });
        
        // Obsługa pobierania logów
        const downloadBtn = document.querySelector('[data-download-logs]');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function() {
                console.log('Downloading logs');
                
                // Symulacja pobierania pliku
                this.disabled = true;
                this.innerHTML = '<span class="spinner inline-block w-4 h-4 border-2 border-t-transparent border-white rounded-full animate-spin mr-2"></span> Pobieranie...';
                
                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = 'Pobierz logi';
                    alert('Plik z logami został pobrany.');
                }, 1500);
            });
        }
    },

    /**
     * Konfiguracja zarządzania kopiami zapasowymi
     */
    setupBackups() {
        const backupSection = document.querySelector('[data-backup-section]');
        if (!backupSection) return;
        
        console.log('Initializing backup management');
        
        // Obsługa przycisku tworzenia kopii zapasowej
        const createBackupBtn = document.querySelector('[data-create-backup]');
        if (createBackupBtn) {
            createBackupBtn.addEventListener('click', function() {
                if (confirm('Czy na pewno chcesz utworzyć nową kopię zapasową?')) {
                    console.log('Creating backup');
                    
                    // Symulacja tworzenia kopii zapasowej
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner inline-block w-4 h-4 border-2 border-t-transparent border-white rounded-full animate-spin mr-2"></span> Tworzenie...';
                    
                    setTimeout(() => {
                        this.disabled = false;
                        this.innerHTML = 'Utwórz kopię zapasową';
                        alert('Kopia zapasowa została utworzona pomyślnie.');
                        
                        // W rzeczywistej implementacji odświeżylibyśmy listę kopii zapasowych
                    }, 3000);
                }
            });
        }
        
        // Obsługa akcji dla kopii zapasowych
        document.querySelectorAll('[data-backup-action]').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-backup-action');
                const backupId = this.closest('tr').getAttribute('data-backup-id');
                
                console.log(`Action ${action} for backup ${backupId}`);
                
                switch (action) {
                    case 'download':
                        console.log(`Downloading backup ${backupId}`);
                        
                        // Symulacja pobierania kopii zapasowej
                        this.disabled = true;
                        setTimeout(() => {
                            this.disabled = false;
                            alert('Kopia zapasowa została pobrana.');
                        }, 1500);
                        break;
                        
                    case 'restore':
                        if (confirm('Czy na pewno chcesz przywrócić tę kopię zapasową? Ta operacja zastąpi aktualne dane.')) {
                            console.log(`Restoring backup ${backupId}`);
                            
                            // Symulacja przywracania kopii zapasowej
                            this.disabled = true;
                            setTimeout(() => {
                                this.disabled = false;
                                alert('Kopia zapasowa została przywrócona pomyślnie.');
                            }, 3000);
                        }
                        break;
                        
                    case 'delete':
                        if (confirm('Czy na pewno chcesz usunąć tę kopię zapasową?')) {
                            console.log(`Deleting backup ${backupId}`);
                            
                            // Symulacja usuwania kopii zapasowej
                            this.disabled = true;
                            setTimeout(() => {
                                this.disabled = false;
                                this.closest('tr').remove();
                                alert('Kopia zapasowa została usunięta.');
                            }, 1000);
                        }
                        break;
                }
            });
        });
    },

    /**
     * Konfiguracja informacji o systemie
     */
    setupSystemInfo() {
        const infoSection = document.querySelector('[data-system-info]');
        if (!infoSection) return;
        
        console.log('Initializing system info');
        
        // Obsługa przycisku odświeżania informacji
        const refreshBtn = document.querySelector('[data-refresh-info]');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                console.log('Refreshing system info');
                
                // Pokaż wskaźnik ładowania
                infoSection.querySelectorAll('[data-info-item]').forEach(item => {
                    item.classList.add('opacity-50');
                });
                
                // Symulacja odświeżania informacji
                setTimeout(() => {
                    infoSection.querySelectorAll('[data-info-item]').forEach(item => {
                        item.classList.remove('opacity-50');
                    });
                    
                    alert('Informacje o systemie zostały zaktualizowane.');
                }, 1000);
            });
        }
    },

    /**
     * Konfiguracja historii logowania
     */
    setupLoginHistory() {
        const historyTable = document.querySelector('[data-login-history]');
        if (!historyTable) return;
        
        console.log('Initializing login history');
        
        // Obsługa filtrowania historii logowania
        const filterForm = document.querySelector('[data-login-filter]');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Filtering login history');
                
                // Pokaż wskaźnik ładowania
                historyTable.classList.add('opacity-50');
                
                // Symulacja filtrowania historii
                setTimeout(() => {
                    historyTable.classList.remove('opacity-50');
                    alert('Historia logowania została przefiltrowana.');
                }, 800);
            });
        }
    }
};

// Eksportujemy moduł
export default systemModule;

// Automatycznie inicjalizujemy moduł przy imporcie
document.addEventListener('DOMContentLoaded', () => {
    systemModule.init();
}); 