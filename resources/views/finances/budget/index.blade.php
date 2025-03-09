@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    console.log('Skrypt został załadowany');
    
    let modal;
    let modalContent;
    let form;
    let methodField;
    let modalTitle;
    let successAlert;

    function openModal(type = null, categoryId = null) {
        console.log('openModal wywołane z typem:', type, 'i ID:', categoryId);
        if (!modal) {
            console.error('Modal nie został zainicjalizowany');
            return;
        }
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);

        // Jeśli mamy ID kategorii, pobieramy dane do edycji
        if (categoryId) {
            fetch(`/finances/budget/${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const category = data.category;
            document.getElementById('name').value = category.name;
            document.getElementById('type').value = category.type;
                        document.getElementById('amount').value = category.amount;
                        document.getElementById('planned_amount').value = category.planned_amount;
            document.getElementById('description').value = category.description || '';
                        
                        modalTitle.textContent = 'Edytuj pozycję';
                        form.action = `{{ route('finances.budget.update', '') }}/${categoryId}`;
                        methodField.innerHTML = `@csrf\n@method('PUT')`;
                    } else {
                        throw new Error(data.error || 'Nie udało się pobrać danych kategorii');
                    }
                })
                .catch(error => {
                    console.error('Błąd podczas pobierania danych kategorii:', error);
                    alert('Wystąpił błąd podczas pobierania danych. Spróbuj ponownie.');
                    closeModal();
                });
        } else {
            // Dodawanie nowej pozycji
            modalTitle.textContent = 'Dodaj nową pozycję';
            form.action = '{{ route("finances.budget.store") }}';
            methodField.innerHTML = '@csrf';
            form.reset();
            
            // Ustawiamy odpowiedni typ w zależności od kafelka
            const typeSelect = document.getElementById('type');
            switch(type) {
                case 'cash':
                    typeSelect.value = 'cash';
                    modalTitle.textContent = 'Dodaj gotówkę';
                    break;
                case 'bank':
                    typeSelect.value = 'company_bank';
                    modalTitle.textContent = 'Dodaj konto bankowe';
                    break;
                case 'investment':
                    typeSelect.value = 'investments';
                    modalTitle.textContent = 'Dodaj inwestycję';
                    break;
            }
        }
    }

    function closeModal() {
        console.log('closeModal wywołane');
        if (!modal || !modalContent) {
            console.error('Modal nie został zainicjalizowany');
            return;
        }
        
        modalContent.classList.add('scale-95', 'opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
            form.reset();
            methodField.innerHTML = '';
            document.getElementById('amount').classList.remove('border-red-500');
        }, 300);
    }

    function showTileDetails(type) {
        const detailsContainer = document.getElementById('tileDetails');
        
        fetch(`/finances/budget/details/${type}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Wystąpił błąd podczas pobierania szczegółów');
                }
                
                let details = '';
                
                switch(type) {
                    case 'cash':
                        details = `
                            <div class="space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <span class="text-xs text-gray-500 block mb-1">Stan kasy</span>
                                        <span class="text-lg font-semibold text-green-600">{{ number_format($cash ?? 0, 2, ',', ' ') }} zł</span>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <span class="text-xs text-gray-500 block mb-1">Zmiana (30 dni)</span>
                                        <span class="text-lg font-semibold text-green-600">+2 500,00 zł</span>
                                    </div>
                                </div>

                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Ostatnie operacje</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between text-sm">
                                            <div>
                                                <p class="font-medium text-gray-700">Wpłata do kasy</p>
                                                <p class="text-xs text-gray-500">22.03.2024</p>
                                            </div>
                                            <span class="font-medium text-green-600">+1 500,00 zł</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <div>
                                                <p class="font-medium text-gray-700">Wypłata z kasy</p>
                                                <p class="text-xs text-gray-500">21.03.2024</p>
                                            </div>
                                            <span class="font-medium text-red-600">-500,00 zł</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Statystyki</h4>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500">Liczba operacji (30 dni)</p>
                                            <p class="font-medium text-gray-700">15</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Średnia wartość operacji</p>
                                            <p class="font-medium text-gray-700">1 250,00 zł</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Największa wpłata</p>
                                            <p class="font-medium text-green-600">5 000,00 zł</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Największa wypłata</p>
                                            <p class="font-medium text-red-600">2 500,00 zł</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex space-x-3">
                                    <button onclick="openModal('cash')" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-600">
                                        Dodaj wpłatę
                                    </button>
                                    <button class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-600">
                                        Dodaj wypłatę
                                    </button>
                                </div>
                            </div>
                        `;
                        break;
                    case 'bank':
                        details = `
                            <div class="space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <span class="text-xs text-gray-500 block mb-1">Stan kont</span>
                                        <span class="text-lg font-semibold text-indigo-600">{{ number_format($bankAccounts ?? 0, 2, ',', ' ') }} zł</span>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <span class="text-xs text-gray-500 block mb-1">Liczba kont</span>
                                        <span class="text-lg font-semibold text-indigo-600">{{ $accountsCount ?? 0 }}</span>
                                    </div>
                                </div>

                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Lista kont</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between text-sm bg-gray-50 p-3 rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-700">mBank - Firmowe</p>
                                                <p class="text-xs text-gray-500">54 1140 2004 0000 0000 0000 0000</p>
                                            </div>
                                            <span class="font-medium text-indigo-600">25 000,00 zł</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm bg-gray-50 p-3 rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-700">ING - Oszczędnościowe</p>
                                                <p class="text-xs text-gray-500">73 1050 1445 1000 0000 0000 0000</p>
                                            </div>
                                            <span class="font-medium text-indigo-600">15 000,00 zł</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Ostatnie przelewy</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between text-sm">
                                            <div>
                                                <p class="font-medium text-gray-700">Przelew przychodzący</p>
                                                <p class="text-xs text-gray-500">22.03.2024 - mBank</p>
                                            </div>
                                            <span class="font-medium text-green-600">+3 500,00 zł</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <div>
                                                <p class="font-medium text-gray-700">Przelew wychodzący</p>
                                                <p class="text-xs text-gray-500">21.03.2024 - ING</p>
                                            </div>
                                            <span class="font-medium text-red-600">-1 200,00 zł</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex space-x-3">
                                    <button onclick="openModal('bank')" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-indigo-500 rounded-md hover:bg-indigo-600">
                                        Dodaj konto
                                    </button>
                                    <button class="flex-1 px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-md hover:bg-gray-600">
                                        Historia operacji
                                    </button>
                                </div>
                            </div>
                        `;
                        break;
                    case 'investment':
                        details = `
                            <div class="space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <span class="text-xs text-gray-500 block mb-1">Wartość inwestycji</span>
                                        <span class="text-lg font-semibold text-amber-600">${data.total} zł</span>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <span class="text-xs text-gray-500 block mb-1">Planowana wartość</span>
                                        <span class="text-lg font-semibold text-green-600">${data.plannedTotal} zł</span>
                                    </div>
                                </div>

                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Lista inwestycji</h4>
                                    <div class="space-y-3">
                                        ${data.investments.map(inv => `
                                            <div class="bg-gray-50 p-4 rounded-lg">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex-1">
                                                        <p class="font-medium text-gray-700">${inv.name}</p>
                                                        ${inv.description ? `<p class="text-xs text-gray-500 mt-1">${inv.description}</p>` : ''}
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <span class="font-medium text-amber-600">${inv.amount} zł</span>
                                                        <button onclick="openModal('investment', ${inv.id})" class="p-1.5 text-gray-500 hover:text-amber-600 transition-colors">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                        <span>Plan: ${inv.planned_amount} zł</span>
                                                        <span class="font-medium ${parseFloat(inv.completion_percentage.replace(',', '.')) >= 100 ? 'text-green-600' : 'text-amber-600'}">
                                                            ${inv.completion_percentage}%
                                                        </span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                        <div class="bg-amber-600 h-1.5 rounded-full" style="width: ${Math.min(parseFloat(inv.completion_percentage.replace(',', '.')), 100)}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>

                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Statystyki</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Aktywne inwestycje</p>
                                            <p class="font-medium text-gray-700">${data.stats.count}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Średnia wartość</p>
                                            <p class="font-medium text-gray-700">${data.stats.average} zł</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Największa inwestycja</p>
                                            <p class="font-medium text-green-600">${data.stats.max} zł</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Realizacja planu</p>
                                            <p class="font-medium text-amber-600">${data.stats.completion}%</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex space-x-3">
                                    <button onclick="openModal('investment')" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-amber-500 rounded-md hover:bg-amber-600">
                                        Dodaj inwestycję
                                    </button>
                                    <button onclick="showTileDetails('investment')" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-md hover:bg-gray-600">
                                        Odśwież dane
                                    </button>
                                </div>
                            </div>
                        `;
                        break;
                    default:
                        details = '<p class="text-sm text-gray-500">Wybierz kafelek, aby zobaczyć szczegóły</p>';
                }
                
                detailsContainer.innerHTML = details;
            })
            .catch(error => {
                console.error('Błąd podczas pobierania szczegółów:', error);
                detailsContainer.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            ${error.message || 'Wystąpił błąd podczas ładowania szczegółów'}
                        </p>
                    </div>
                `;
            });
    }

    function refreshHistory() {
        fetch('{{ route("finances.budget.history") }}')
            .then(response => response.json())
            .then(data => {
                const historyContainer = document.querySelector('#historyEntries');
                let historyHtml = '';
                
                data.entries.forEach(entry => {
                    const amountClass = entry.type === 'cash' ? 'text-green-600' : 
                                      entry.type === 'company_bank' ? 'text-indigo-600' : 
                                      'text-amber-600';
                    
                    historyHtml += `
                        <div class="border-b border-gray-100 pb-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">${entry.name}</p>
                                    <p class="text-xs text-gray-500">${entry.created_at}</p>
                                </div>
                                <span class="text-sm font-medium ${amountClass}">+ ${entry.amount} zł</span>
                            </div>
                        </div>
                    `;
                });
                
                historyContainer.innerHTML = historyHtml;
            })
            .catch(error => console.error('Błąd podczas odświeżania historii:', error));
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded wywołane');
        
        modal = document.getElementById('categoryModal');
        modalContent = document.getElementById('modalContent');
        form = document.getElementById('categoryForm');
        methodField = document.getElementById('methodField');
        modalTitle = document.getElementById('modalTitle');
        successAlert = document.getElementById('successAlert');

        // Odświeżamy historię przy załadowaniu strony
        refreshHistory();

        // Dodajemy nasłuchiwanie na przycisk "Nowa kategoria"
        const newCategoryBtn = document.querySelector('button[data-action="new-category"]');
        if (newCategoryBtn) {
            newCategoryBtn.addEventListener('click', () => openModal());
        }

        // Dodajemy nasłuchiwanie na przyciski edycji
        document.querySelectorAll('button[data-action="edit-category"]').forEach(button => {
            button.addEventListener('click', () => {
                const category = JSON.parse(button.dataset.category);
                openModal(category.type);
            });
        });

        // Walidacja formularza
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Formularz wysłany');
                
                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const method = formData.get('_method') || 'POST';
                
                console.log('Token CSRF:', csrfToken);
                console.log('Metoda:', method);
                console.log('Dane formularza:', Object.fromEntries(formData));
                console.log('URL formularza:', this.action);

                fetch(this.action, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(async response => {
                    console.log('Status odpowiedzi:', response.status);
                    console.log('Nagłówki odpowiedzi:', Object.fromEntries(response.headers.entries()));
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Otrzymano nieprawidłową odpowiedź od serwera');
                    }

                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        closeModal();
                        // Odśwież historię i szczegóły
                        refreshHistory();
                        const activeType = document.querySelector('#type').value;
                        if (activeType) {
                            showTileDetails(activeType === 'investments' ? 'investment' : activeType);
                        }
                        // Pokaż komunikat sukcesu
                        const successMessage = document.createElement('div');
                        successMessage.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                        successMessage.textContent = data.message;
                        document.body.appendChild(successMessage);
                        setTimeout(() => {
                            successMessage.style.opacity = '0';
                            successMessage.style.transition = 'opacity 0.5s ease-in-out';
                            setTimeout(() => successMessage.remove(), 500);
                        }, 3000);
                    } else {
                        throw new Error(data.error || 'Wystąpił nieznany błąd');
                    }
                })
                .catch(error => {
                    console.error('Błąd podczas wysyłania:', error);
                    alert('Wystąpił błąd podczas zapisywania danych: ' + error.message);
                });
            });
        }

        // Zamknij modal po kliknięciu poza nim
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                    refreshHistory();
                }
            });
        }

        // Obsługa klawisza ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // Ukryj alert sukcesu po 3 sekundach
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.opacity = '0';
                successAlert.style.transition = 'opacity 0.5s ease-in-out';
                setTimeout(() => successAlert.remove(), 500);
            }, 3000);
        }

        // Dodajemy nasłuchiwanie na kliknięcia w kafelki
        const cashTile = document.querySelector('[onclick="openModal(\'cash\')"]').closest('.bg-white');
        const bankTile = document.querySelector('[onclick="openModal(\'bank\')"]').closest('.bg-white');
        const investmentTile = document.querySelector('[onclick="openModal(\'investment\')"]').closest('.bg-white');

        cashTile.addEventListener('click', () => showTileDetails('cash'));
        bankTile.addEventListener('click', () => showTileDetails('bank'));
        investmentTile.addEventListener('click', () => showTileDetails('investment'));
    });
</script>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-[1920px] mx-auto sm:px-4 lg:px-6">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ __('Budżet') }}</h2>
        </div>
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Menu boczne -->
            <div class="md:w-56 flex-shrink-0">
                <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <div class="space-y-2">
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-home text-gray-600 text-xs"></i>
                                </span>
                                <span class="text-sm">Panel Główny</span>
                            </a>
                            <a href="{{ route('finances.index') }}" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-file-invoice-dollar text-gray-600 text-xs"></i>
                                </span>
                                <span class="text-sm">Finanse</span>
                            </a>
                            <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-book text-gray-600 text-xs"></i>
                                </span>
                                <span class="text-sm">Księgowość</span>
                            </a>
                            <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-warehouse text-gray-600 text-xs"></i>
                                </span>
                                <span class="text-sm">Magazyn</span>
                            </a>
                            <a href="{{ route('contractors.index') }}" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-users text-gray-600 text-xs"></i>
                                </span>
                                <span class="text-sm">Kontrahenci</span>
                            </a>
                            <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-tasks text-gray-600 text-xs"></i>
                                </span>
                                <span class="text-sm">Zadania</span>
                            </a>
                            <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-chart-bar text-gray-600 text-xs"></i>
                                </span>
                                <span class="text-sm">Raporty</span>
                            </a>
                            <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-file-contract text-gray-600 text-xs"></i>
                                </span>
                                <span class="text-sm">Umowy</span>
                            </a>
                            <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-calculator text-gray-600 text-xs"></i>
                                </span>
                                <span class="text-sm">Kosztorysy</span>
                            </a>
                        </div>
                    </div>
                        </div>
                <!-- Notatki -->
                <div class="mt-4">
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Notatki</h3>
                            <div class="space-y-3">
                                <div class="flex items-start space-x-3 text-xs">
                                    <div class="w-6 h-6 rounded-lg bg-yellow-100 bg-opacity-10 flex items-center justify-center mt-0.5">
                                        <i class="fas fa-sticky-note text-yellow-600 text-xs"></i>
                            </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-700">Spotkanie z klientem</p>
                                        <p class="text-gray-500 text-xs">Omówienie projektu - 15:00</p>
                        </div>
                    </div>
                                <div class="flex items-start space-x-3 text-xs">
                                    <div class="w-6 h-6 rounded-lg bg-blue-100 bg-opacity-10 flex items-center justify-center mt-0.5">
                                        <i class="fas fa-thumbtack text-blue-600 text-xs"></i>
                        </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-700">Przygotować raport</p>
                                        <p class="text-gray-500 text-xs">Do końca tygodnia</p>
                        </div>
                    </div>
                                <button class="w-full flex items-center justify-center space-x-2 text-gray-700 hover:text-gray-900 group mt-2">
                                    <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                        <i class="fas fa-plus text-gray-600 text-xs"></i>
                                    </span>
                                    <span class="text-xs">Dodaj notatkę</span>
                                </button>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>

            <!-- Główna zawartość -->
            <div class="flex-1">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <!-- Pierwszy wiersz - główne wskaźniki -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- Suma -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-[160px]">
                                <div class="p-6 h-full flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">SUMA AKTYWÓW</p>
                                            <p class="text-2xl font-semibold text-blue-600">{{ number_format($totalAssets ?? 0, 2, ',', ' ') }} zł</p>
                                        </div>
                                        <span class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-wallet text-blue-600"></i>
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Zmiana (30 dni)</span>
                                            <span class="text-green-600">
                                                <i class="fas fa-arrow-up"></i> 15%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gotówka -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-[160px]">
                                <div class="p-6 h-full flex flex-col justify-between">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">GOTÓWKA</p>
                                            <p class="text-2xl font-semibold text-green-600">{{ number_format($cash ?? 0, 2, ',', ' ') }} zł</p>
                                        </div>
                                        <button onclick="openModal('cash')" class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center hover:bg-green-200 transition-colors">
                                            <i class="fas fa-plus text-green-600"></i>
                                        </button>
                                    </div>
                                    <div class="mt-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Stan kasy</span>
                                            <button onclick="openModal('cash')" class="text-green-600 hover:text-green-700">
                                                Dodaj/Edytuj
                                            </button>
                                        </div>
                                    </div>
                        </div>
                    </div>

                            <!-- Konta bankowe -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-[160px]">
                                <div class="p-6 h-full flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">KONTA BANKOWE</p>
                                            <p class="text-2xl font-semibold text-indigo-600">{{ number_format($bankAccounts ?? 0, 2, ',', ' ') }} zł</p>
                                        </div>
                                        <button onclick="openModal('bank')" class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center hover:bg-indigo-200 transition-colors">
                                            <i class="fas fa-plus text-indigo-600"></i>
                                        </button>
                                    </div>
                                    <div class="mt-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Liczba kont: {{ $accountsCount ?? 0 }}</span>
                                            <button onclick="openModal('bank')" class="text-indigo-600 hover:text-indigo-700">
                                                Dodaj/Edytuj
                                            </button>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>

                        <!-- Drugi wiersz - pozostałe kafelki -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Przychody -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-[160px]">
                                <div class="p-6 h-full flex flex-col justify-between">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">PRZYCHODY</p>
                                            <p class="text-2xl font-semibold text-emerald-600">{{ number_format($income ?? 0, 2, ',', ' ') }} zł</p>
                        </div>
                                        <span class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center">
                                            <i class="fas fa-chart-line text-emerald-600"></i>
                                        </span>
                            </div>
                                    <div class="mt-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Ten miesiąc</span>
                                            <span class="text-emerald-600">
                                                {{ number_format($monthlyIncome ?? 0, 2, ',', ' ') }} zł
                                            </span>
                        </div>
                    </div>
                        </div>
                        </div>
                        
                            <!-- Wydatki -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-[160px]">
                                <div class="p-6 h-full flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">WYDATKI</p>
                                            <p class="text-2xl font-semibold text-red-600">{{ number_format($expenses ?? 0, 2, ',', ' ') }} zł</p>
                                        </div>
                                        <span class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                                            <i class="fas fa-chart-bar text-red-600"></i>
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Ten miesiąc</span>
                                            <span class="text-red-600">
                                                {{ number_format($monthlyExpenses ?? 0, 2, ',', ' ') }} zł
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Inwestycje -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-[160px]">
                                <div class="p-6 h-full flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">INWESTYCJE</p>
                                            <p class="text-2xl font-semibold text-amber-600">{{ number_format($investments ?? 0, 2, ',', ' ') }} zł</p>
                                        </div>
                                        <button onclick="openModal('investment')" class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center hover:bg-amber-200 transition-colors">
                                            <i class="fas fa-plus text-amber-600"></i>
                                        </button>
                                    </div>
                                    <div class="mt-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Zwrot</span>
                                            <button onclick="openModal('investment')" class="text-amber-600 hover:text-amber-700">
                                                Dodaj/Edytuj
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                        </div>

                        <!-- Sekcja z historią i szczegółami -->
                        <div class="mt-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Historia wpisów -->
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Historia wpisów</h3>
                                        <div id="historyEntries" class="space-y-4">
                                            <!-- Tu będą dynamicznie wstawiane wpisy -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Szczegóły aktywnego kafelka -->
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Szczegóły</h3>
                                        <div id="tileDetails" class="space-y-4">
                                            <p class="text-sm text-gray-500">Wybierz kafelek, aby zobaczyć szczegóły</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal dodawania/edycji kategorii -->
<div class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto transition-opacity duration-300 ease-in-out" id="categoryModal">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full transform transition-transform duration-300 ease-in-out scale-95 opacity-0" id="modalContent">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Dodaj kategorię budżetu</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="categoryForm" action="{{ route('finances.budget.store') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div id="methodField"></div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nazwa</label>
                            <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Typ</label>
                            <select name="type" id="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="investments">Inwestycje</option>
                                <option value="cash">Gotówka</option>
                                <option value="company_bank">Konto firmowe</option>
                                <option value="private_bank">Konto prywatne</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kwota</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" step="0.01" name="amount" id="amount" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">zł</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Planowana kwota</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" step="0.01" name="planned_amount" id="planned_amount" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">zł</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Opis</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Anuluj</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">Zapisz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div id="successAlert" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
    {{ session('success') }}
</div>
@endif

@endsection 