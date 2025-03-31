import './bootstrap';
import './utils';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import focus from '@alpinejs/focus';

// Importuję magazyn theme
import setupThemeStore from './stores/theme';

// Dodaj import Turbo dla obsługi nawigacji
import * as Turbo from '@hotwired/turbo';

// Sprawdź, czy Alpine już istnieje (zapobiega wielokrotnym instancjom)
if (!window.Alpine) {
    // Konfiguracja frameworków
    Alpine.plugin(persist);
    Alpine.plugin(focus);
    window.Alpine = Alpine;

    // Oznaczamy, że Alpine został zainicjalizowany
    window._alpineInitialized = true;

    // Inicjalizuję magazyn theme przed uruchomieniem Alpine
    setupThemeStore();
    
    document.addEventListener('DOMContentLoaded', () => {
        Alpine.start();
        console.log('Alpine uruchomiony - pierwsza instancja');
    });
} else {
    // Upewnij się, że magazyn theme został zainicjalizowany
    if (!Alpine.store('theme')) {
        setupThemeStore();
        console.log('Magazyn theme zainicjalizowany dla istniejącej instancji Alpine');
    }
    console.log('Alpine już istnieje - unikam wielokrotnych instancji');
}

// Funkcja inicjalizująca Turbo - tylko raz
let _turboInitialized = false;
function initializeTurbo() {
    if (_turboInitialized) {
        console.log('Turbo już zainicjalizowane - pomijam');
        return;
    }
    
    _turboInitialized = true;
    console.log('Inicjalizacja Turbo - pierwsze uruchomienie');
    
    // Konfiguracja Turbo
    Turbo.session.drive = true;
    
    // Ograniczenie liczby równoczesnych żądań Turbo
    configureTurboRequests();
    
    // Konfiguracja nasłuchiwania zdarzeń
    setupTurboListeners();
}

// Konfiguracja ograniczenia równoczesnych żądań
function configureTurboRequests() {
    let currentRequests = 0;
    const maxConcurrentRequests = 2;
    const originalFetch = window.fetch;

    window.fetch = function(...args) {
        // Sprawdź, czy to jest żądanie Turbo
        const isTurboRequest = args[1]?.headers?.accept?.includes('text/html');
        
        if (isTurboRequest) {
            if (currentRequests >= maxConcurrentRequests) {
                // Odrzuć nadmiarowe żądania
                return Promise.reject(new Error('Too many concurrent requests'));
            }
            
            currentRequests++;
            
            return originalFetch(...args)
                .then(response => {
                    currentRequests--;
                    return response;
                })
                .catch(error => {
                    currentRequests--;
                    throw error;
                });
        }
        
        // Dla nie-Turbo żądań, przekaż dalej
        return originalFetch(...args);
    };
}

// Uruchom inicjalizację Turbo po załadowaniu strony
document.addEventListener('DOMContentLoaded', () => {
    initializeTurbo();
    
    // Naprawa linków nawigacyjnych
    fixNavigationLinks();
    
    // Ładowanie dodatkowych modułów
    loadModules();
});

// Dodanie obsługi interakcji między Turbo i Alpine
document.addEventListener('turbo:load', () => {
    // Sprawdź, czy Alpine jest gotowy
    if (window.Alpine) {
        // Sprawdź, czy magazyn theme istnieje i zainicjalizuj go, jeśli nie
        if (!Alpine.store('theme')) {
            setupThemeStore();
            console.log('Magazyn theme zainicjalizowany podczas turbo:load');
        }
        
        // Odśwież komponenty Alpine
        document.querySelectorAll('[x-data]').forEach(el => {
            if (el.__x) {
                el.__x.updateElements(el);
            }
        });
    }
    
    // Naprawa linków nawigacyjnych
    fixNavigationLinks();
    
    // Ładowanie dodatkowych modułów
    loadModules();
});

// Naprawa linków nawigacyjnych, aby poprawnie działały z Turbo
function fixNavigationLinks() {
    // Logi diagnostyczne
    console.log('Fixing navigation links...');
    
    // Zliczanie linków przed naprawą
    const allLinks = document.querySelectorAll('a[href]:not([data-turbo="false"])');
    const unfixedLinks = document.querySelectorAll('a[href]:not([data-turbo="false"]):not([data-fixed="true"])');
    console.log(`Total links: ${allLinks.length}, Unfixed links: ${unfixedLinks.length}`);
    
    document.querySelectorAll('a[href]:not([data-turbo="false"]):not([data-fixed="true"])').forEach(link => {
        // Pomijamy linki zewnętrzne, maile, nr telefonów, itp.
        if (
            link.getAttribute('href').startsWith('#') ||
            link.getAttribute('href').startsWith('http') ||
            link.getAttribute('href').startsWith('mailto:') ||
            link.getAttribute('href').startsWith('tel:') ||
            link.getAttribute('target') === '_blank'
        ) {
            // Oznaczamy te linki również, aby nie były sprawdzane ponownie
            link.setAttribute('data-fixed', 'true');
            return;
        }
        
        // Oznaczamy link jako już naprawiony
        link.setAttribute('data-fixed', 'true');
        link.setAttribute('data-turbo', 'true');
        
        // Dodajemy obsługę kliknięcia tylko jeśli link nie ma jeszcze obsługi
        if (!link._clickHandlerAdded) {
            link._clickHandlerAdded = true;
            
            link.addEventListener('click', (e) => {
                // Zapobiegamy wielokrotnemu kliknięciu podczas przetwarzania
                if (link.getAttribute('data-processing') === 'true') {
                    e.preventDefault();
                    console.log('Preventing multiple clicks on link:', link.href);
                    return;
                }
                
                // Dodajemy klasę aktywną
                link.classList.add('pointer-events-none', 'opacity-70');
                link.setAttribute('data-processing', 'true');
                
                // Dodajemy efekt ładowania
                document.body.classList.add('turbo-loading');
                
                // Resetujemy stan po zakończeniu nawigacji lub po timeout
                setTimeout(() => {
                    link.classList.remove('pointer-events-none', 'opacity-70');
                    link.removeAttribute('data-processing');
                }, 1000);
            });
        }
    });
    
    // Log po naprawie
    console.log('Navigation links fixed');
}

// Dodaj funkcję debugowania na stronie
function showDebugMessage(message, type = 'info') {
    // Sprawdź czy tryb debugowania jest włączony
    const debugMode = document.documentElement.getAttribute('data-debug') === 'true';
    if (!debugMode) return;

    // Bardziej widoczne logi konsolowe
    console.log('%c[DEBUG] ' + message, 'font-weight: bold; font-size: 14px; color: ' + (type === 'error' ? 'red' : 'blue') + '; background: #f0f0f0; padding: 3px 5px; border-radius: 3px;');
    
    // Dodaj wiadomość do strony
    let debugContainer = document.getElementById('js-debug-messages');
    if (!debugContainer) {
        debugContainer = document.createElement('div');
        debugContainer.id = 'js-debug-messages';
        debugContainer.className = 'fixed top-2 left-2 z-[9999] flex flex-col gap-2 max-w-xs';
        document.body.appendChild(debugContainer);
    }
    
    const msgElement = document.createElement('div');
    msgElement.className = 'bg-rose-600 text-white font-medium text-base p-3 rounded-lg shadow-xl border-2 border-white';
    
    // Dodanie ikony zależnie od typu wiadomości
    const icon = type === 'error' ? '❌' : '🔍';
    msgElement.textContent = `${icon} ${message}`;
    
    debugContainer.appendChild(msgElement);
    
    // Usuń po 10 sekundach (dłuższy czas)
    setTimeout(() => {
        try {
            debugContainer.removeChild(msgElement);
        } catch (e) {}
    }, 10000);
}

// Funkcja wyświetlania informacji o wersji
function showVersionInfo() {
    // Sprawdź czy tryb debugowania jest włączony
    const debugMode = document.documentElement.getAttribute('data-debug') === 'true';
    if (!debugMode) return;

    // Pokaż informacje o wersji
    const versionElement = document.createElement('div');
    versionElement.className = 'fixed bottom-2 left-2 bg-indigo-600 text-white text-base font-bold p-3 rounded-lg shadow-xl z-[9999] border-2 border-white';
    versionElement.textContent = `👾 Wersja: 1.3.1 | ${new Date().toLocaleString()}`;
    document.body.appendChild(versionElement);
    
    // Wyświetl także informacje o przeglądarce
    showDebugMessage(`Przeglądarka: ${navigator.userAgent.substring(0, 50)}...`);
}

// Konfiguracja nasłuchiwania zdarzeń Turbo
function setupTurboListeners() {
    // Log wersji
    console.log('Konfiguracja Turbo - v1.3.1');
    showDebugMessage('Konfiguracja Turbo v1.3.1');
    
    // Pokaż informacje o wersji
    showVersionInfo();
    
    // Przed rozpoczęciem nawigacji
    document.addEventListener('turbo:before-visit', (event) => {
        document.body.classList.add('turbo-loading');
        showDebugMessage('Rozpoczynam nawigację: ' + event.detail?.url);
        
        // Zapis stanu menu przed nawigacją
        const adminSidebarOpen = localStorage.getItem('admin_sidebar_state');
        if (adminSidebarOpen) {
            // Zapisz w sesji stan menu przed nawigacją (krótkotrwałe przechowywanie)
            sessionStorage.setItem('_preserve_sidebar_state', adminSidebarOpen);
            console.log('Zapisano stan menu przed nawigacją:', adminSidebarOpen);
            showDebugMessage('Zapisano stan menu: ' + adminSidebarOpen);
        }
    });
    
    // Po zakończeniu nawigacji
    document.addEventListener('turbo:visit', () => {
        document.body.classList.remove('turbo-loading');
    });
    
    // Obsługa błędów Turbo
    document.addEventListener('turbo:load', () => {
        document.body.classList.remove('turbo-loading');
        showDebugMessage('Strona załadowana');
        
        // Przywróć stan menu po nawigacji
        const preservedState = sessionStorage.getItem('_preserve_sidebar_state');
        if (preservedState) {
            console.log('Przywracanie stanu menu po nawigacji:', preservedState);
            showDebugMessage('Przywracanie stanu menu: ' + preservedState);
            
            localStorage.setItem('admin_sidebar_state', preservedState);
            
            // Opcjonalnie - aktualizacja UI jeśli Alpine jeszcze nie zaktualizował stanu
            setTimeout(() => {
                const adminSection = document.querySelector('[data-section="admin"]');
                if (adminSection) {
                    console.log('Wysyłanie zdarzenia odświeżenia menu');
                    showDebugMessage('Odświeżanie menu');
                    adminSection.dispatchEvent(new CustomEvent('refresh-sidebar'));
                }
            }, 100);
        }
    });
    
    // Obsługa błędów
    document.addEventListener('turbo:load', (event) => {
        if (event.detail?.error) {
            errorHandler(event.detail.error);
            showDebugMessage('Błąd ładowania: ' + event.detail.error.message, 'error');
        }
    });
}

// Dynamiczne ładowanie modułów zależne od sekcji strony
const loadedModules = new Set();

async function loadModules() {
    try {
        // Debug - identyfikator wywołania
        const callId = Math.random().toString(36).substring(2, 8);
        
        // Sprawdzenie czy moduły są już załadowane
        if (window.modulesLoaded) {
            console.log(`[loadModules:${callId}] Moduły już załadowane, pomijam duplikat wywołania`);
            return;
        }
        
        // Dodanie flagi aby zapobiec wielokrotnym ładowaniom
        window.modulesLoaded = true;
        
        console.log(`[loadModules:${callId}] Próba załadowania modułów dla strony: ${window.location.pathname}`);
        
        // Sprawdzanie sekcji admin
        const adminSection = document.querySelector('[data-section="admin"]');
        if (adminSection && !loadedModules.has('admin')) {
            try {
                console.log(`[loadModules:${callId}] Ładowanie modułu admin...`);
                loadedModules.add('admin'); // Dodaj wcześniej do zestawu, aby zapobiec duplikatom
                
                // Dodaj obsługę stabilności menu (zapobiega rozwijaniu/zwijaniu)
                const sidebarState = localStorage.getItem('admin_sidebar_state');
                console.log(`[loadModules:${callId}] Stan menu zapisany w localStorage: ${sidebarState}`);
                
                // Powiadom, że administratorzy są załadowani
                adminSection.dispatchEvent(new CustomEvent('admin-module-ready'));
                
                const adminModule = await import('./admin/index.js');
                console.log(`[loadModules:${callId}] Moduł admin załadowany pomyślnie`);
            } catch (error) {
                console.error(`[loadModules:${callId}] Nie udało się załadować modułu admin:`, error);
            }
        }
        
        // Sprawdzanie sekcji dashboard
        const dashboardSection = document.querySelector('[data-section="dashboard"]');
        if (dashboardSection && !loadedModules.has('dashboard')) {
            try {
                console.log(`[loadModules:${callId}] Ładowanie modułu dashboard...`);
                loadedModules.add('dashboard'); // Dodaj wcześniej do zestawu, aby zapobiec duplikatom
                const dashboardModule = await import('./dashboard.js');
                console.log(`[loadModules:${callId}] Moduł dashboard załadowany pomyślnie`);
            } catch (error) {
                console.error(`[loadModules:${callId}] Nie udało się załadować modułu dashboard:`, error);
            }
        }
    } catch (error) {
        errorHandler(error);
    }
}

// Obsługa trybu ciemnego
document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('darkMode', isDark ? 'true' : 'false');
        });
        
        // Ustawienie początkowego stanu
        if (localStorage.getItem('darkMode') === 'true' || 
            (window.matchMedia('(prefers-color-scheme: dark)').matches && !localStorage.getItem('darkMode'))) {
            document.documentElement.classList.add('dark');
        }
    }
});
