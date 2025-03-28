import './bootstrap';
import './utils';

import Alpine from 'alpinejs';
import AlpinePersist from '@alpinejs/persist';
import Focus from '@alpinejs/focus';

// Dodaj import Turbo dla obsługi nawigacji
import * as Turbo from '@hotwired/turbo';

// Inicjalizacja Alpine.js
window.Alpine = Alpine;
Alpine.plugin(AlpinePersist);
Alpine.plugin(Focus);

// Inicjalizacja Turbo
window.Turbo = Turbo;

// Obsługa nawigacji bez przeładowania strony
document.addEventListener('turbo:before-render', () => {
    // Opcjonalne: dodaj efekt ładowania strony
    document.body.classList.add('turbo-loading');
});

document.addEventListener('turbo:render', () => {
    // Opcjonalne: usuń efekt ładowania po zakończeniu
    document.body.classList.remove('turbo-loading');
});

Alpine.start();

// Dodaj style dla efektu ładowania
const style = document.createElement('style');
style.textContent = `
    .turbo-loading {
        opacity: 0.7;
        transition: opacity 0.3s;
    }
`;
document.head.appendChild(style);

// Dynamiczne ładowanie skryptów admin
const loadedModules = new Set();

document.addEventListener('DOMContentLoaded', async () => {
    const adminSection = document.querySelector('[data-section="admin"]');
    if (adminSection && !loadedModules.has('admin')) {
        await import('./admin/index');
        loadedModules.add('admin');

        const rolesSection = document.querySelector('[data-section="roles"]');
        if (rolesSection && !loadedModules.has('roles')) {
            await import('./admin/roles');
            loadedModules.add('roles');
        }
    }
});

// Obsługa responsywnego menu bocznego
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.sidebar');
    const menuButton = document.querySelector('.menu-button');
    const mainContent = document.querySelector('.main-content');

    if (menuButton && sidebar) {
        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            if (mainContent) {
                mainContent.classList.toggle('ml-0');
                mainContent.classList.toggle('ml-[250px]');
            }
        });

        // Zamykanie menu po kliknięciu poza nim na urządzeniach mobilnych
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !menuButton.contains(e.target) && 
                sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                if (mainContent) {
                    mainContent.classList.remove('ml-0');
                    mainContent.classList.add('ml-[250px]');
                }
            }
        });
    }
});
