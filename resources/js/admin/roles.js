document.addEventListener('DOMContentLoaded', function() {
    // Sprawdzamy, czy jesteśmy na stronie zarządzania rolami
    const modal = document.querySelector('[role="dialog"]');
    if (!modal) return; // Jeśli nie ma modalu, kończymy wykonywanie skryptu

    const addRoleButton = document.querySelector('button[data-action="add-role"]');
    const editButtons = document.querySelectorAll('button[data-action="edit-role"]');
    const deleteButtons = document.querySelectorAll('button[data-action="delete-role"]');
    const cancelButton = modal.querySelector('button[data-action="cancel"]');
    const form = modal.querySelector('form');

    // Funkcja do otwierania modalu
    function openModal() {
        modal.classList.remove('hidden');
    }

    // Funkcja do zamykania modalu
    function closeModal() {
        modal.classList.add('hidden');
        form.reset();
        form.action = '/admin/roles/create';
        form.querySelector('input[name="_method"]')?.remove();
    }

    // Obsługa dodawania nowej roli
    if (addRoleButton) {
        addRoleButton.addEventListener('click', () => {
            openModal();
        });
    }

    // Obsługa edycji roli
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('tr');
            const roleName = row.querySelector('td:first-child').textContent.trim();
            const roleId = button.dataset.roleId;
            const permissions = Array.from(row.querySelectorAll('.bg-blue-100'))
                .map(span => span.textContent.trim());

            form.action = `/admin/roles/${roleId}/edit`;
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            form.querySelector('#role_name').value = roleName;
            form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = permissions.includes(checkbox.value);
            });

            openModal();
        });
    });

    // Obsługa usuwania roli
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const roleId = button.dataset.roleId;
            if (confirm('Czy na pewno chcesz usunąć tę rolę?')) {
                window.location.href = `/admin/roles/${roleId}`;
            }
        });
    });

    // Zamykanie modalu
    if (cancelButton) {
        cancelButton.addEventListener('click', closeModal);
    }

    // Zamykanie modalu po kliknięciu poza nim
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Obsługa klawisza ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
}); 