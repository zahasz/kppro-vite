<x-admin-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Ustawienia powiadomień</h1>
                </div>
                
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
                
                <form action="{{ route('admin.notifications.update-settings') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-2">Ogólne ustawienia</h2>
                        
                        <div class="flex items-center mb-4">
                            <input id="email_notifications" name="email_notifications" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                @if(auth()->user()->notification_settings['email_notifications'] ?? false) checked @endif>
                            <label for="email_notifications" class="ml-2 block text-sm text-gray-900">
                                Powiadomienia e-mail
                                <p class="text-sm text-gray-500">Otrzymuj powiadomienia na adres e-mail.</p>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-2">Typy powiadomień</h2>
                        
                        <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                            <div class="flex items-center">
                                <input id="invoice_notifications" name="invoice_notifications" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    @if(auth()->user()->notification_settings['invoice_notifications'] ?? true) checked @endif>
                                <label for="invoice_notifications" class="ml-2 block text-sm text-gray-900">
                                    Faktury
                                    <p class="text-sm text-gray-500">Powiadomienia o wystawionych fakturach.</p>
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input id="subscription_notifications" name="subscription_notifications" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    @if(auth()->user()->notification_settings['subscription_notifications'] ?? true) checked @endif>
                                <label for="subscription_notifications" class="ml-2 block text-sm text-gray-900">
                                    Subskrypcje
                                    <p class="text-sm text-gray-500">Powiadomienia o wygasających subskrypcjach, odnowieniach i zmianach.</p>
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input id="report_notifications" name="report_notifications" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    @if(auth()->user()->notification_settings['report_notifications'] ?? true) checked @endif>
                                <label for="report_notifications" class="ml-2 block text-sm text-gray-900">
                                    Raporty
                                    <p class="text-sm text-gray-500">Powiadomienia o wygenerowanych raportach tygodniowych i miesięcznych.</p>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition">
                            Zapisz ustawienia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout> 