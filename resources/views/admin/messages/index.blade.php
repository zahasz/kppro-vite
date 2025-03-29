<x-admin-layout>
    @section('title', 'Zarządzanie wiadomościami')
    @section('header', 'Zarządzanie wiadomościami')

    <div class="space-y-6">
        <!-- Karta statystyk -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-5 bg-gradient-to-r from-blue-600 to-indigo-600">
                <h3 class="text-lg font-semibold text-white">Statystyki wiadomości</h3>
            </div>
            
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 text-white mr-4">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <p class="text-sm text-blue-800">Wszystkie wiadomości</p>
                                <p class="text-2xl font-bold text-blue-900">{{ App\Models\Message::count() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 text-white mr-4">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <p class="text-sm text-green-800">Aktywni użytkownicy</p>
                                <p class="text-2xl font-bold text-green-900">{{ App\Models\User::has('sentMessages')->count() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-amber-50 p-4 rounded-lg border border-amber-100">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-amber-500 text-white mr-4">
                                <i class="fas fa-envelope-open"></i>
                            </div>
                            <div>
                                <p class="text-sm text-amber-800">Nieprzeczytane</p>
                                <p class="text-2xl font-bold text-amber-900">{{ App\Models\Message::where('is_read', false)->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 flex justify-between items-center">
                    <a href="{{ route('admin.messages.statistics') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-chart-line mr-1"></i> Szczegółowe statystyki
                    </a>
                    
                    <a href="{{ route('admin.messages.mass') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-paper-plane mr-2"></i> Wyślij masową wiadomość
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Lista wiadomości -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-5 bg-gradient-to-r from-gray-700 to-gray-900">
                <h3 class="text-lg font-semibold text-white">Wszystkie wiadomości</h3>
            </div>
            
            <div class="p-5">
                @if($messages->isEmpty())
                    <p class="text-gray-500 italic text-center py-6">Brak wiadomości w systemie</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nadawca</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Odbiorca</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($messages as $message)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $message->sender->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $message->recipient->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $message->subject }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $message->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $message->is_read ? 'Przeczytane' : 'Nieprzeczytane' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.messages.show', $message->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Szczegóły</a>
                                            <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Czy na pewno chcesz usunąć tę wiadomość?')">Usuń</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Top użytkownicy -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-5 bg-gradient-to-r from-purple-600 to-purple-800">
                <h3 class="text-lg font-semibold text-white">Najbardziej aktywni użytkownicy</h3>
            </div>
            
            <div class="p-5">
                @if($userMessageStats->isEmpty())
                    <p class="text-gray-500 italic text-center py-6">Brak danych o aktywności użytkowników</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Najczęściej piszący -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Najczęściej piszący:</h4>
                            <ul class="space-y-2">
                                @foreach($userMessageStats->sortByDesc('sent_messages_count')->take(5) as $user)
                                    <li class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-800 font-semibold mr-3">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <span class="text-sm">{{ $user->name }}</span>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800">
                                            {{ $user->sent_messages_count }} wiadomości
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <!-- Najczęściej otrzymujący -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Najczęściej otrzymujący:</h4>
                            <ul class="space-y-2">
                                @foreach($userMessageStats->sortByDesc('received_messages_count')->take(5) as $user)
                                    <li class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-800 font-semibold mr-3">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <span class="text-sm">{{ $user->name }}</span>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                            {{ $user->received_messages_count }} wiadomości
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout> 