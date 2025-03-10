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
