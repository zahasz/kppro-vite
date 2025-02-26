<!-- Szablon główny -->
<x-app-layout>
    <div class="py-6">
        <div class="max-w-[1920px] mx-auto sm:px-4 lg:px-6">
            <!-- Nagłówek -->
            <div class="mb-6 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800">{{ __('Kontrahenci') }}</h2>
                <div class="flex items-center space-x-2">
                    <button onclick="printList()" class="btn flex items-center space-x-2 bg-white bg-opacity-10 hover:bg-opacity-20 text-gray-700 px-4 py-2 rounded-lg transition-all duration-200">
                        <i class="fas fa-print"></i>
                        <span class="text-sm">Drukuj</span>
                    </button>
                    <button onclick="exportToPDF()" class="btn flex items-center space-x-2 bg-white bg-opacity-10 hover:bg-opacity-20 text-gray-700 px-4 py-2 rounded-lg transition-all duration-200">
                        <i class="fas fa-file-pdf"></i>
                        <span class="text-sm">PDF</span>
                    </button>
                    <button onclick="showAddModal()" class="btn flex items-center space-x-2 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-all duration-200">
                        <i class="fas fa-plus"></i>
                        <span class="text-sm">Dodaj kontrahenta</span>
                    </button>
                </div>
            </div>

            <!-- Filtry i wyszukiwanie -->
            <div class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="col-span-2">
                    <div class="relative">
                        <input type="text" placeholder="Szukaj..." class="w-full pl-10 pr-4 py-2 rounded-lg bg-white bg-opacity-10 focus:bg-opacity-20 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                    </div>
                </div>
                <div>
                    <select class="w-full py-2 px-4 rounded-lg bg-white bg-opacity-10 focus:bg-opacity-20 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Status</option>
                        <option value="active">Aktywny</option>
                        <option value="inactive">Nieaktywny</option>
                        <option value="blocked">Zablokowany</option>
                    </select>
                </div>
                <div>
                    <select class="w-full py-2 px-4 rounded-lg bg-white bg-opacity-10 focus:bg-opacity-20 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Typ</option>
                        <option value="client">Klient</option>
                        <option value="supplier">Dostawca</option>
                        <option value="both">Klient i dostawca</option>
                    </select>
                </div>
            </div>

            <!-- Lista kontrahentów -->
            <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 divide-opacity-10">
                        <thead>
                            <tr class="text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                <th class="px-4 py-3">Nazwa / NIP</th>
                                <th class="px-4 py-3">Kontakt</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Akcje</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 divide-opacity-10">
                            @forelse ($contractors as $contractor)
                            <tr class="hover:bg-white hover:bg-opacity-5 transition-all duration-200">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 bg-opacity-10 flex items-center justify-center mr-3">
                                            <i class="fas fa-building text-blue-500"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $contractor->company_name }}</div>
                                            <div class="text-xs text-gray-500">NIP: {{ $contractor->nip }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ $contractor->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $contractor->phone }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($contractor->status === 'active')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 bg-opacity-20">
                                            Aktywny
                                        </span>
                                    @elseif($contractor->status === 'inactive')
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 bg-opacity-20">
                                            Nieaktywny
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 bg-opacity-20">
                                            Zablokowany
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button onclick="showEditModal({{ $contractor->id }})" class="p-1 hover:text-blue-600 transition-colors duration-200">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="showDetailsModal({{ $contractor->id }})" class="p-1 hover:text-blue-600 transition-colors duration-200">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="showDeleteModal({{ $contractor->id }})" class="p-1 hover:text-red-600 transition-colors duration-200">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    Brak kontrahentów do wyświetlenia
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Paginacja -->
                <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 border-opacity-10">
                    <div class="text-xs text-gray-700">
                        Pokazano {{ $contractors->firstItem() ?? 0 }}-{{ $contractors->lastItem() ?? 0 }} z {{ $contractors->total() ?? 0 }} wyników
                    </div>
                    <div class="flex items-center space-x-2">
                        {{ $contractors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal - Dodaj/Edytuj -->
    <div id="contractorModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="min-h-screen px-4 text-center">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="contractorForm" onsubmit="event.preventDefault(); saveContractor();">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">
                                    Dodaj kontrahenta
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="companyName" class="block text-sm font-medium text-gray-700">Nazwa firmy</label>
                                        <input type="text" id="companyName" name="company_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
                                        <input type="text" id="nip" name="nip" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" id="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                                        <input type="tel" id="phone" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                            <option value="active">Aktywny</option>
                                            <option value="inactive">Nieaktywny</option>
                                            <option value="blocked">Zablokowany</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-500 text-base font-medium text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Zapisz
                        </button>
                        <button type="button" onclick="hideModal('contractorModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Anuluj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal - Szczegóły -->
    <div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="min-h-screen px-4 text-center">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Szczegóły kontrahenta
                            </h3>
                            <div class="space-y-4" id="contractorDetails">
                                <!-- Tutaj będą wyświetlane szczegóły -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="hideModal('detailsModal')" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Zamknij
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal - Usuń -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="min-h-screen px-4 text-center">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Usuń kontrahenta
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Czy na pewno chcesz usunąć tego kontrahenta? Ta operacja jest nieodwracalna.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="deleteContractor()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Usuń
                    </button>
                    <button type="button" onclick="hideModal('deleteModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Anuluj
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentContractorId = null;

        // Konfiguracja nagłówków dla fetch
        function getHeaders() {
            return {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            };
        }

        function showAddModal() {
            currentContractorId = null;
            document.getElementById('modalTitle').textContent = 'Dodaj kontrahenta';
            document.getElementById('contractorForm').reset();
            showModal('contractorModal');
        }

        function showEditModal(id) {
            currentContractorId = id;
            document.getElementById('modalTitle').textContent = 'Edytuj kontrahenta';
            loadContractorData(id);
            showModal('contractorModal');
        }

        function showDetailsModal(id) {
            loadContractorDetails(id);
            showModal('detailsModal');
        }

        function showDeleteModal(id) {
            currentContractorId = id;
            showModal('deleteModal');
        }

        function showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function hideModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        async function saveContractor() {
            const form = document.getElementById('contractorForm');
            const formData = new FormData(form);

            try {
                let url = '/contractors';
                let method = 'POST';

                if (currentContractorId) {
                    url = `/contractors/${currentContractorId}`;
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (!response.ok) {
                    if (result.errors) {
                        const errorMessages = Object.values(result.errors).flat().join('\n');
                        throw new Error(errorMessages);
                    }
                    throw new Error(result.message || 'Wystąpił błąd podczas zapisywania');
                }

                hideModal('contractorModal');
                window.location.reload();
            } catch (error) {
                console.error('Błąd podczas zapisywania kontrahenta:', error);
                alert(error.message || 'Wystąpił błąd podczas zapisywania kontrahenta');
            }
        }

        async function loadContractorData(id) {
            try {
                const response = await fetch(`/contractors/${id}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        ...getHeaders()
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Nie udało się załadować danych');
                }

                const data = await response.json();
                const form = document.getElementById('contractorForm');
                
                form.elements['company_name'].value = data.company_name;
                form.elements['nip'].value = data.nip;
                form.elements['email'].value = data.email;
                form.elements['phone'].value = data.phone;
                form.elements['status'].value = data.status;
            } catch (error) {
                console.error('Błąd podczas ładowania danych kontrahenta:', error);
                alert('Nie udało się załadować danych kontrahenta');
                hideModal('contractorModal');
            }
        }

        async function loadContractorDetails(id) {
            try {
                const response = await fetch(`/contractors/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        ...getHeaders()
                    }
                });

                if (!response.ok) {
                    throw new Error('Nie udało się załadować szczegółów');
                }

                const data = await response.json();
                
                const detailsHtml = `
                    <div class="grid grid-cols-2 gap-4">
                        <div class="font-medium">Nazwa firmy:</div>
                        <div>${data.company_name}</div>
                        <div class="font-medium">NIP:</div>
                        <div>${data.nip}</div>
                        <div class="font-medium">Email:</div>
                        <div>${data.email}</div>
                        <div class="font-medium">Telefon:</div>
                        <div>${data.phone}</div>
                        <div class="font-medium">Status:</div>
                        <div>${data.status === 'active' ? 'Aktywny' : 
                              data.status === 'inactive' ? 'Nieaktywny' : 'Zablokowany'}</div>
                    </div>
                `;
                document.getElementById('contractorDetails').innerHTML = detailsHtml;
            } catch (error) {
                console.error('Błąd podczas ładowania szczegółów kontrahenta:', error);
                alert('Nie udało się załadować szczegółów kontrahenta');
                hideModal('detailsModal');
            }
        }

        async function deleteContractor() {
            if (currentContractorId) {
                try {
                    const formData = new FormData();
                    formData.append('_method', 'DELETE');

                    const response = await fetch(`/contractors/${currentContractorId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        const result = await response.json();
                        throw new Error(result.message || 'Nie udało się usunąć kontrahenta');
                    }

                    hideModal('deleteModal');
                    window.location.reload();
                } catch (error) {
                    console.error('Błąd podczas usuwania kontrahenta:', error);
                    alert(error.message || 'Nie udało się usunąć kontrahenta');
                }
            }
        }

        function printList() {
            window.print();
        }

        async function exportToPDF() {
            try {
                const response = await fetch('/contractors/export-pdf', {
                    method: 'POST',
                    headers: getHeaders()
                });

                if (!response.ok) {
                    throw new Error('Nie udało się wygenerować PDF');
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'kontrahenci.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Błąd podczas eksportu do PDF:', error);
                alert('Nie udało się wyeksportować listy do PDF');
            }
        }
    </script>
    @endpush
</x-app-layout> 