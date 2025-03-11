import './bootstrap';
import './utils';

import Alpine from 'alpinejs';

// Inicjalizacja Alpine.js
window.Alpine = Alpine;
Alpine.start();

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
