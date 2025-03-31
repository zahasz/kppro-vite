<x-admin-layout>
    <x-slot name="header">
        Użytkownicy online
    </x-slot>

    <div class="space-y-6" data-section="admin.users">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-base font-medium text-gray-900">Aktywni użytkownicy</h2>
                <p class="text-sm text-gray-500">
                    Użytkownicy, którzy byli aktywni w ciągu ostatnich 5 minut
                </p>
            </div>
            
            <div class="p-4">
                <div id="users-list" class="min-h-[200px]">
                    <div class="flex justify-center items-center h-32">
                        <svg class="animate-spin h-8 w-8 text-steel-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usersList = document.getElementById('users-list');
            
            function loadOnlineUsers() {
                fetch('/admin/users/online')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && Array.isArray(data.data)) {
                            if (data.data.length === 0) {
                                usersList.innerHTML = '<div class="text-center py-10 text-gray-500">Brak użytkowników online</div>';
                                return;
                            }
                            
                            let html = '<div class="overflow-x-auto">';
                            html += '<table class="min-w-full divide-y divide-gray-200">';
                            html += '<thead class="bg-gray-50"><tr>';
                            html += '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Użytkownik</th>';
                            html += '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>';
                            html += '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ostatnio widziany</th>';
                            html += '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>';
                            html += '</tr></thead>';
                            html += '<tbody class="bg-white divide-y divide-gray-200">';
                            
                            data.data.forEach(user => {
                                html += '<tr>';
                                html += `<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${user.name}</td>`;
                                html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.email}</td>`;
                                html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.last_seen}</td>`;
                                html += `<td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Online
                                    </span>
                                </td>`;
                                html += '</tr>';
                            });
                            
                            html += '</tbody></table></div>';
                            usersList.innerHTML = html;
                        } else {
                            usersList.innerHTML = '<div class="text-center py-10 text-red-500">Wystąpił błąd podczas ładowania danych</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        usersList.innerHTML = '<div class="text-center py-10 text-red-500">Wystąpił błąd podczas ładowania danych</div>';
                    });
            }
            
            // Ładuj użytkowników od razu i potem co 30 sekund
            loadOnlineUsers();
            setInterval(loadOnlineUsers, 30000);
        });
    </script>
    @endpush
</x-admin-layout> 