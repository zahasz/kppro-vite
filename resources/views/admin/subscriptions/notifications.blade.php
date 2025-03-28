<x-admin-layout>
    <x-slot name="header">
        Powiadomienia subskrypcji
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-base font-medium text-gray-900">Wszystkie powiadomienia</h2>
            <div class="flex space-x-2">
                <button class="px-3 py-1.5 bg-steel-blue-100 text-steel-blue-700 rounded hover:bg-steel-blue-200 text-xs font-medium">
                    Oznacz wszystkie jako przeczytane
                </button>
            </div>
        </div>

        <div class="divide-y divide-gray-200">
            @if(count($notifications) > 0)
                @foreach($notifications as $notification)
                    <div class="p-4 hover:bg-gray-50 {{ $notification->read ? '' : 'bg-steel-blue-50' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ $notification->title }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $notification->message }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex space-x-2">
                                @if(!$notification->read)
                                    <button class="text-xs text-steel-blue-600 hover:text-steel-blue-800">
                                        Oznacz jako przeczytane
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-4 text-center text-gray-500">
                    Brak nowych powiadomień
                </div>
            @endif
        </div>
    </div>

    <div class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-base font-medium text-gray-900">Ustawienia powiadomień</h2>
        </div>

        <div class="p-4">
            <form method="POST" action="#" class="space-y-4">
                @csrf
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Otrzymuj powiadomienia o:</h3>
                    
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input id="new_subscription" name="new_subscription" type="checkbox" class="h-4 w-4 text-steel-blue-600 focus:ring-steel-blue-500 border-gray-300 rounded" checked>
                            <label for="new_subscription" class="ml-2 block text-sm text-gray-700">Nowych subskrypcjach</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="ending_subscription" name="ending_subscription" type="checkbox" class="h-4 w-4 text-steel-blue-600 focus:ring-steel-blue-500 border-gray-300 rounded" checked>
                            <label for="ending_subscription" class="ml-2 block text-sm text-gray-700">Kończących się subskrypcjach</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="failed_payment" name="failed_payment" type="checkbox" class="h-4 w-4 text-steel-blue-600 focus:ring-steel-blue-500 border-gray-300 rounded" checked>
                            <label for="failed_payment" class="ml-2 block text-sm text-gray-700">Nieudanych płatnościach</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="renewal" name="renewal" type="checkbox" class="h-4 w-4 text-steel-blue-600 focus:ring-steel-blue-500 border-gray-300 rounded" checked>
                            <label for="renewal" class="ml-2 block text-sm text-gray-700">Odnowieniach subskrypcji</label>
                        </div>
                    </div>
                </div>
                
                <div class="pt-3">
                    <button type="submit" class="px-4 py-2 bg-steel-blue-600 text-white rounded hover:bg-steel-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-steel-blue-500 text-sm">
                        Zapisz ustawienia
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout> 