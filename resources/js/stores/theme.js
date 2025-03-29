/**
 * Store Alpine.js do zarządzania motywem ciemnym/jasnym
 */
export default function setupThemeStore() {
    Alpine.store('theme', {
        isDark: localStorage.getItem('dark_mode') === 'true',
        userChosen: localStorage.getItem('user_chosen_theme') === 'true',
        
        toggle() {
            this.isDark = !this.isDark;
            this.userChosen = true;
            localStorage.setItem('dark_mode', this.isDark);
            localStorage.setItem('user_chosen_theme', 'true');
            this.updateTheme();
        },
        
        updateTheme() {
            if (this.isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            // Opcjonalnie: Aktualizacja zmiennych CSS
            this.updateCssVariables();
        },
        
        updateCssVariables() {
            // Tutaj można zaktualizować zmienne CSS w zależności od motywu
            // Przykład:
            // document.documentElement.style.setProperty('--bg-color', this.isDark ? '#121212' : '#ffffff');
        },
        
        resetToSystemPreference() {
            this.userChosen = false;
            localStorage.removeItem('user_chosen_theme');
            this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            localStorage.setItem('dark_mode', this.isDark);
            this.updateTheme();
        },
        
        init() {
            // Sprawdź preferencje systemu (opcjonalnie)
            if (localStorage.getItem('dark_mode') === null) {
                this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                localStorage.setItem('dark_mode', this.isDark);
            }
            
            // Zastosuj motyw
            this.updateTheme();
            
            // Nasłuchuj zmian preferencji systemowych
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                // Tylko zmieniaj automatycznie jeśli użytkownik nie ustawił ręcznie
                if (!this.userChosen) {
                    this.isDark = e.matches;
                    localStorage.setItem('dark_mode', this.isDark);
                    this.updateTheme();
                }
            });
        }
    });
} 