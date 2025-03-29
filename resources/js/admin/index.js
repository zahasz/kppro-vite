/**
 * Moduł centralny dla panelu administracyjnego
 * Zawiera logikę używaną na wszystkich stronach administracyjnych
 * Inne moduły są ładowane dynamicznie w zależności od potrzeb
 */

// Inicjalizacja głównych funkcji panelu administratora
document.addEventListener('DOMContentLoaded', () => {
    console.log('Admin module loaded');
    
    // Wykrywanie sekcji, by określić jakie dodatkowe moduły załadować
    const detectSections = () => {
        const sections = {
            'dashboard': document.querySelector('[data-section="admin.dashboard"]'),
            'users': document.querySelector('[data-section="admin.users"]'),
            'roles': document.querySelector('[data-section="admin.roles"]'),
            'subscriptions': document.querySelector('[data-section="admin.subscriptions"]'),
            'revenue': document.querySelector('[data-section="admin.revenue"]'),
            'system': document.querySelector('[data-section="admin.system"]'),
        };
        
        return sections;
    };
    
    // Ładowanie funkcji specyficznych dla konkretnej sekcji
    const loadSectionSpecific = async () => {
        const sections = detectSections();
        
        try {
            // Ładowanie modułu dashboardu administratora
            if (sections.dashboard) {
                adminStats.initDashboardStats();
            }
            
            // Ładowanie dynamiczne pozostałych modułów
            if (sections.users) {
                // @vite-ignore
                import('./users.js').then(module => {
                    console.log('Users module loaded');
                    if (typeof module.default === 'function') {
                        module.default();
                    } else if (module.default) {
                        module.default.init();
                    }
                }).catch(error => {
                    console.error('Failed to load users module:', error);
                });
            }
            
            if (sections.roles) {
                // @vite-ignore
                import('./roles.js').then(module => {
                    console.log('Roles module loaded');
                    if (typeof module.default === 'function') {
                        module.default();
                    } else if (module.default) {
                        module.default.init();
                    }
                }).catch(error => {
                    console.error('Failed to load roles module:', error);
                });
            }
            
            if (sections.subscriptions) {
                // @vite-ignore
                import('./subscriptions.js').then(module => {
                    console.log('Subscriptions module loaded');
                    if (typeof module.default === 'function') {
                        module.default();
                    } else if (module.default) {
                        module.default.init();
                    }
                }).catch(error => {
                    console.error('Failed to load subscriptions module:', error);
                });
            }
            
            if (sections.revenue) {
                // @vite-ignore
                import('./reports.js').then(module => {
                    console.log('Reports module loaded');
                    if (typeof module.default === 'function') {
                        module.default();
                    } else if (module.default) {
                        module.default.init();
                    }
                }).catch(error => {
                    console.error('Failed to load reports module:', error);
                });
            }
            
            if (sections.system) {
                // @vite-ignore
                import('./system.js').then(module => {
                    console.log('System module loaded');
                    if (typeof module.default === 'function') {
                        module.default();
                    } else if (module.default) {
                        module.default.init();
                    }
                }).catch(error => {
                    console.error('Failed to load system module:', error);
                });
            }
        } catch (error) {
            console.error('Error in admin module:', error);
        }
    };
    
    // Inicjalizacja funkcji
    loadSectionSpecific();
});

// Przykładowy moduł statystyk używany na wielu stronach administracyjnych
export const adminStats = {
    initDashboardStats() {
        console.log('Dashboard stats initialized');
        // Kod inicjalizujący statystyki
    },
    
    refreshStats() {
        console.log('Stats refreshed');
        // Kod odświeżający statystyki
    }
}; 