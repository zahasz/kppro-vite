@extends('layouts.app')

@section('head')
<script>
    console.log('Skrypt został załadowany');
    
    let modal;
    let modalContent;
    let form;
    let methodField;
    let modalTitle;
    let successAlert;

    function openModal(category = null) {
        console.log('openModal wywołane', category);
        if (!modal) {
            console.error('Modal nie został zainicjalizowany');
            return;
        }
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);

        if (category) {
            modalTitle.textContent = 'Edytuj kategorię budżetu';
            form.action = '{{ url("/finances/budget") }}/' + category.id;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('name').value = category.name;
            document.getElementById('type').value = category.type;
            document.getElementById('amount').value = Number(category.amount).toFixed(2);
            document.getElementById('description').value = category.description || '';
        } else {
            modalTitle.textContent = 'Dodaj kategorię budżetu';
            form.action = '{{ route("finances.budget.store") }}';
            methodField.innerHTML = '';
            form.reset();
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

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded wywołane');
        
        modal = document.getElementById('categoryModal');
        modalContent = document.getElementById('modalContent');
        form = document.getElementById('categoryForm');
        methodField = document.getElementById('methodField');
        modalTitle = document.getElementById('modalTitle');
        successAlert = document.getElementById('successAlert');

        // Dodajemy nasłuchiwanie na przycisk "Nowa kategoria"
        const newCategoryBtn = document.querySelector('button[data-action="new-category"]');
        if (newCategoryBtn) {
            newCategoryBtn.addEventListener('click', () => openModal());
        }

        // Dodajemy nasłuchiwanie na przyciski edycji
        document.querySelectorAll('button[data-action="edit-category"]').forEach(button => {
            button.addEventListener('click', () => {
                const category = JSON.parse(button.dataset.category);
                openModal(category);
            });
        });

        // Walidacja formularza
        if (form) {
            form.addEventListener('submit', function(e) {
                const amount = document.getElementById('amount');
                let hasErrors = false;
                
                // Resetujemy style błędów
                amount.classList.remove('border-red-500');
                
                if (isNaN(amount.value) || amount.value === '' || parseFloat(amount.value) < 0) {
                    e.preventDefault();
                    amount.classList.add('border-red-500');
                    alert('Proszę podać prawidłową kwotę aktualną (nie może być ujemna)');
                    hasErrors = true;
                }

                if (!hasErrors) {
                    // Formatujemy kwoty przed wysłaniem
                    amount.value = parseFloat(amount.value).toFixed(2);
                }
            });
        }

        // Zamknij modal po kliknięciu poza nim
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
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
    });
</script>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-[1920px] mx-auto sm:px-4 lg:px-6">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ __('Budżet') }}</h2>
            <div class="flex items-center space-x-4">
                <button class="bg-white bg-opacity-10 backdrop-blur-sm px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-gray-900 flex items-center space-x-2">
                    <i class="fas fa-file-export text-gray-500"></i>
                    <span>Eksportuj</span>
                </button>
                <button data-action="new-category" class="bg-blue-500 px-4 py-2 rounded-lg text-sm font-medium text-white hover:bg-blue-600 flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Nowa kategoria</span>
                </button>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Menu boczne -->
            @include('layouts.sidebar')

            <!-- Główna zawartość -->
            <div class="flex-1">
                <!-- Statystyki budżetu -->
                <div class="grid grid-cols-1 md:grid-cols-7 gap-4 mb-8">
                    <!-- Łączna kwota -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-semibold text-gray-700">Łączna kwota</h3>
                            <span class="text-[10px] text-gray-500">Aktualny stan</span>
                        </div>
                        @php
                            $totalAmount = 
                                $categories->get('cash', collect())->sum('amount') +
                                $categories->get('company_bank', collect())->sum('amount') +
                                $categories->get('private_bank', collect())->sum('amount') +
                                $categories->get('investments', collect())->sum('amount') +
                                $categories->get('leasing', collect())->sum('amount') +
                                $categories->get('loans_given', collect())->sum('amount') +
                                $categories->get('invoices_receivable', collect())->sum('amount') -
                                $categories->get('loans_taken', collect())->sum('amount') -
                                $categories->get('invoices_to_pay', collect())->sum('amount') -
                                $categories->get('salaries_to_pay', collect())->sum('amount');

                            $totalPlanned = 
                                $categories->get('cash', collect())->sum('planned_amount') +
                                $categories->get('company_bank', collect())->sum('planned_amount') +
                                $categories->get('private_bank', collect())->sum('planned_amount') +
                                $categories->get('investments', collect())->sum('planned_amount') +
                                $categories->get('leasing', collect())->sum('planned_amount') +
                                $categories->get('loans_given', collect())->sum('planned_amount') +
                                $categories->get('invoices_receivable', collect())->sum('planned_amount') -
                                $categories->get('loans_taken', collect())->sum('planned_amount') -
                                $categories->get('invoices_to_pay', collect())->sum('planned_amount') -
                                $categories->get('salaries_to_pay', collect())->sum('planned_amount');
                        @endphp
                        <p class="text-xl font-bold text-gray-800 mb-1">{{ number_format($totalAmount, 2, ',', ' ') }} zł</p>
                        <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $totalPlanned > 0 ? ($totalAmount / $totalPlanned * 100) : 0 }}%"></div>
                        </div>
                        <div class="mt-1 flex items-center justify-between text-[10px]">
                            <span class="text-gray-600">Plan: {{ number_format($totalPlanned, 2, ',', ' ') }} zł</span>
                            <span class="text-gray-600">{{ $totalPlanned > 0 ? number_format($totalAmount / $totalPlanned * 100, 0) : 0 }}%</span>
                        </div>
                    </div>

                    <!-- Gotówka -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-semibold text-gray-700">Gotówka</h3>
                            <span class="text-[10px] text-green-500">Stan kasy</span>
                        </div>
                        <p class="text-xl font-bold text-gray-800 mb-1">{{ number_format($categories->get('cash', collect())->sum('amount'), 2, ',', ' ') }} zł</p>
                        <div class="space-y-1">
                            @foreach($categories->get('cash', collect()) as $cash)
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-gray-600">{{ $cash->name }}:</span>
                                <span class="font-medium text-gray-800">{{ number_format($cash->amount, 2, ',', ' ') }} zł</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Konta bankowe -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-semibold text-gray-700">Konta bankowe</h3>
                            <span class="text-[10px] text-blue-500">Stan kont</span>
                        </div>
                        <p class="text-xl font-bold text-gray-800 mb-1">{{ number_format($categories->get('company_bank', collect())->merge($categories->get('private_bank', collect()))->sum('amount'), 2, ',', ' ') }} zł</p>
                        <div class="space-y-1">
                            @foreach($categories->get('company_bank', collect())->merge($categories->get('private_bank', collect())) as $bank)
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-gray-600">{{ $bank->name }}:</span>
                                <span class="font-medium text-gray-800">{{ number_format($bank->amount, 2, ',', ' ') }} zł</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pożyczki zaciągnięte -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-semibold text-gray-700">Pożyczki zaciągnięte</h3>
                            <span class="text-[10px] text-red-500">Do spłaty</span>
                        </div>
                        <p class="text-xl font-bold text-red-600 mb-1">-{{ number_format($categories->get('loans_taken', collect())->sum('amount'), 2, ',', ' ') }} zł</p>
                        <div class="space-y-1">
                            @foreach($categories->get('loans_taken', collect()) as $loan)
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-gray-600">{{ $loan->name }}:</span>
                                <span class="font-medium text-red-600">-{{ number_format($loan->amount, 2, ',', ' ') }} zł</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pożyczki udzielone -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-semibold text-gray-700">Pożyczki udzielone</h3>
                            <span class="text-[10px] text-green-500">Do odzyskania</span>
                        </div>
                        <p class="text-xl font-bold text-green-600 mb-1">{{ number_format($categories->get('loans_given', collect())->sum('amount'), 2, ',', ' ') }} zł</p>
                        <div class="space-y-1">
                            @foreach($categories->get('loans_given', collect()) as $loan)
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-gray-600">{{ $loan->name }}:</span>
                                <span class="font-medium text-green-600">{{ number_format($loan->amount, 2, ',', ' ') }} zł</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Inwestycje -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-semibold text-gray-700">Inwestycje</h3>
                            <span class="text-[10px] text-purple-500">Wartość</span>
                        </div>
                        <p class="text-xl font-bold text-gray-800 mb-1">{{ number_format($categories->get('investments', collect())->sum('amount'), 2, ',', ' ') }} zł</p>
                        <div class="space-y-1">
                            @foreach($categories->get('investments', collect()) as $category)
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-lg bg-purple-100 bg-opacity-50 flex items-center justify-center">
                                            <i class="fas fa-chart-line text-purple-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-[11px] font-semibold text-gray-800">{{ $category->name }}</h4>
                                            <p class="text-[10px] text-gray-500">{{ number_format($category->amount, 2, ',', ' ') }} zł / {{ number_format($category->planned_amount, 2, ',', ' ') }} zł</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <button data-action="edit-category" data-category="{{ $category->toJson() }}" class="text-blue-600 hover:text-blue-700 text-xs">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('finances.budget.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tę kategorię?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="h-1 bg-gray-200 rounded-full overflow-hidden mt-1">
                                    <div class="h-full bg-purple-500 rounded-full" style="width: {{ $category->planned_amount > 0 ? ($category->amount / $category->planned_amount * 100) : 0 }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Leasing -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-semibold text-gray-700">Leasing</h3>
                            <span class="text-[10px] text-indigo-500">Wartość</span>
                        </div>
                        <p class="text-xl font-bold text-gray-800 mb-1">{{ number_format($categories->get('leasing', collect())->sum('amount'), 2, ',', ' ') }} zł</p>
                        <div class="space-y-1">
                            @foreach($categories->get('leasing', collect()) as $category)
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-lg bg-indigo-100 bg-opacity-50 flex items-center justify-center">
                                            <i class="fas fa-car text-indigo-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-[11px] font-semibold text-gray-800">{{ $category->name }}</h4>
                                            <p class="text-[10px] text-gray-500">{{ number_format($category->amount, 2, ',', ' ') }} zł</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col space-y-1">
                                        <button data-action="edit-category" data-category="{{ $category->toJson() }}" class="text-blue-600 hover:text-blue-700 text-xs">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('finances.budget.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tę kategorię?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Faktury do opłacenia -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-semibold text-gray-700">Faktury do opłacenia</h3>
                            <span class="text-[10px] text-red-500">Do zapłaty</span>
                        </div>
                        <p class="text-xl font-bold text-red-600 mb-1">-{{ number_format($categories->get('invoices_to_pay', collect())->sum('amount'), 2, ',', ' ') }} zł</p>
                        <div class="space-y-1">
                            @foreach($categories->get('invoices_to_pay', collect()) as $category)
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-gray-600">{{ $category->name }}:</span>
                                <span class="font-medium text-red-600">-{{ number_format($category->amount, 2, ',', ' ') }} zł</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Należności z faktur -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-semibold text-gray-700">Należności z faktur</h3>
                            <span class="text-[10px] text-green-500">Do otrzymania</span>
                        </div>
                        <p class="text-xl font-bold text-green-600 mb-1">{{ number_format($categories->get('invoices_receivable', collect())->sum('amount'), 2, ',', ' ') }} zł</p>
                        <div class="space-y-1">
                            @foreach($categories->get('invoices_receivable', collect()) as $category)
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-gray-600">{{ $category->name }}:</span>
                                <span class="font-medium text-green-600">{{ number_format($category->amount, 2, ',', ' ') }} zł</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Wynagrodzenia do rozliczenia -->
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-semibold text-gray-700">Wynagrodzenia do rozliczenia</h3>
                            <span class="text-[10px] text-orange-500">Do wypłaty</span>
                        </div>
                        <p class="text-xl font-bold text-orange-600 mb-1">-{{ number_format($categories->get('salaries_to_pay', collect())->sum('amount'), 2, ',', ' ') }} zł</p>
                        <div class="space-y-1">
                            @foreach($categories->get('salaries_to_pay', collect()) as $category)
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-gray-600">{{ $category->name }}:</span>
                                <span class="font-medium text-orange-600">-{{ number_format($category->amount, 2, ',', ' ') }} zł</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Kategorie budżetu -->
                <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Kategorie budżetu</h3>
                        </div>
                        
                        <div class="space-y-2">
                            <!-- Gotówka -->
                            @foreach($categories->get('cash', collect()) as $category)
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-lg bg-green-100 bg-opacity-50 flex items-center justify-center">
                                            <i class="fas fa-money-bill text-green-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-[11px] font-semibold text-gray-800">{{ $category->name }}</h4>
                                            <p class="text-[10px] text-gray-500">{{ number_format($category->amount, 2, ',', ' ') }} zł</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col space-y-1">
                                        <button data-action="edit-category" data-category="{{ $category->toJson() }}" class="text-blue-600 hover:text-blue-700 text-xs">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('finances.budget.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tę kategorię?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            <!-- Konta bankowe -->
                            @foreach($categories->get('company_bank', collect())->merge($categories->get('private_bank', collect())) as $category)
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-lg bg-blue-100 bg-opacity-50 flex items-center justify-center">
                                            <i class="fas fa-university text-blue-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-[11px] font-semibold text-gray-800">{{ $category->name }}</h4>
                                            <p class="text-[10px] text-gray-500">{{ number_format($category->amount, 2, ',', ' ') }} zł</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col space-y-1">
                                        <button data-action="edit-category" data-category="{{ $category->toJson() }}" class="text-blue-600 hover:text-blue-700 text-xs">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('finances.budget.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tę kategorię?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            <!-- Pożyczki -->
                            @foreach($categories->get('loans_taken', collect())->merge($categories->get('loans_given', collect())) as $category)
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-lg bg-amber-100 bg-opacity-50 flex items-center justify-center">
                                            <i class="fas fa-hand-holding-usd text-amber-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-[11px] font-semibold text-gray-800">{{ $category->name }}</h4>
                                            <p class="text-[10px] text-gray-500">{{ number_format($category->amount, 2, ',', ' ') }} zł</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col space-y-1">
                                        <button data-action="edit-category" data-category="{{ $category->toJson() }}" class="text-blue-600 hover:text-blue-700 text-xs">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('finances.budget.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć tę kategorię?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
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
                    @csrf
                    <div id="methodField"></div>
                    @if($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nazwa</label>
                            <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Typ</label>
                            <select name="type" id="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach(App\Models\BudgetCategory::TYPES as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stan aktualny</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" step="0.01" name="amount" id="amount" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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