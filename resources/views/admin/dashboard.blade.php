<x-admin-layout>
    <x-slot name="header">
        Panel Administratora - Przegląd
    </x-slot>

    <div class="space-y-6" data-section="admin.dashboard">
        <!-- Statystyki systemu -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Statystyki systemu</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                <div class="p-4">
                    <div class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors" onclick="window.location.href='{{ route('admin.users.index') }}'">
                        <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Użytkownicy</p>
                            <p class="text-xl font-semibold text-gray-900">{{ \App\Models\User::count() }}</p>
                            <p class="text-xs text-gray-500">Aktywnych: {{ \App\Models\User::where('is_active', true)->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors" onclick="window.location.href='{{ route('admin.subscriptions.index') }}'">
                        <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Subskrypcje</p>
                            <p class="text-xl font-semibold text-gray-900">{{ \App\Models\Subscription::count() }}</p>
                            <p class="text-xs text-emerald-500">Aktywnych: {{ \App\Models\Subscription::where('status', 'active')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors" onclick="window.location.href='{{ route('admin.revenue.dashboard') }}'">
                        <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Przychody</p>
                            <p class="text-xl font-semibold text-gray-900">{{ number_format(\App\Models\Subscription::sum('price'), 2) }} zł</p>
                            <p class="text-xs text-gray-500">W tym miesiącu: {{ number_format(\App\Models\Subscription::whereMonth('created_at', now()->month)->sum('price'), 2) }} zł</p>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors" onclick="window.location.href='{{ route('admin.users.online') }}'">
                        <div class="p-2 rounded-full bg-steel-blue-100 text-steel-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Aktywność</p>
                            <p class="text-xl font-semibold text-gray-900" id="online-users-count">{{ \App\Models\User::where('last_seen_at', '>=', now()->subMinutes(5))->count() }}</p>
                            <p class="text-xs text-gray-500">Użytkowników online</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statystyki płatności i subskrypcji -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Statystyki płatności i subskrypcji</h2>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-4">
                <!-- Wykres subskrypcji -->
                <div class="lg:col-span-2">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Przychody z subskrypcji (ostatnie 6 miesięcy)</h3>
                    <div class="bg-white p-3 rounded-lg border border-gray-200 h-64">
                        <canvas id="subscriptionChart"></canvas>
                    </div>
                </div>
                <!-- Statystyki szczegółowe -->
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Szczegółowe statystyki</h3>
                    <div class="space-y-4">
                        <div class="bg-steel-blue-50 p-4 rounded-lg border border-steel-blue-100">
                            <p class="text-sm font-medium text-gray-700">Średnia wartość subskrypcji</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @php
                                    $subCount = \App\Models\Subscription::count();
                                    $avgPrice = $subCount > 0 ? \App\Models\Subscription::sum('price') / $subCount : 0;
                                @endphp
                                {{ number_format($avgPrice, 2) }} zł
                            </p>
                            <p class="text-xs text-gray-500">Na podstawie {{ $subCount }} subskrypcji</p>
                        </div>
                        
                        <div class="bg-steel-blue-50 p-4 rounded-lg border border-steel-blue-100">
                            <p class="text-sm font-medium text-gray-700">Przychody według statusu</p>
                            <div class="mt-2 space-y-2">
                                @php
                                    $activeRevenue = \App\Models\Subscription::where('status', 'active')->sum('price');
                                    $pendingRevenue = \App\Models\Subscription::where('status', 'pending')->sum('price');
                                    $cancelledRevenue = \App\Models\Subscription::where('status', 'cancelled')->sum('price');
                                    $totalRevenue = $activeRevenue + $pendingRevenue + $cancelledRevenue;
                                    
                                    $activePercent = $totalRevenue > 0 ? ($activeRevenue / $totalRevenue) * 100 : 0;
                                    $pendingPercent = $totalRevenue > 0 ? ($pendingRevenue / $totalRevenue) * 100 : 0;
                                    $cancelledPercent = $totalRevenue > 0 ? ($cancelledRevenue / $totalRevenue) * 100 : 0;
                                @endphp
                                
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-xs text-gray-700">Aktywne</span>
                                        <span class="text-xs text-gray-700">{{ number_format($activeRevenue, 2) }} zł ({{ number_format($activePercent, 1) }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $activePercent }}%"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-xs text-gray-700">Oczekujące</span>
                                        <span class="text-xs text-gray-700">{{ number_format($pendingRevenue, 2) }} zł ({{ number_format($pendingPercent, 1) }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $pendingPercent }}%"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-xs text-gray-700">Anulowane</span>
                                        <span class="text-xs text-gray-700">{{ number_format($cancelledRevenue, 2) }} zł ({{ number_format($cancelledPercent, 1) }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $cancelledPercent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-steel-blue-50 p-4 rounded-lg border border-steel-blue-100">
                            <p class="text-sm font-medium text-gray-700">Porównanie (m/m)</p>
                            @php
                                $currentMonth = \App\Models\Subscription::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->sum('price');
                                    
                                $lastMonth = \App\Models\Subscription::whereMonth('created_at', now()->subMonth()->month)
                                    ->whereYear('created_at', now()->subMonth()->year)
                                    ->sum('price');
                                    
                                $change = $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;
                            @endphp
                            
                            <p class="text-lg font-semibold {{ $change >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}%
                            </p>
                            <p class="text-xs text-gray-500">
                                Obecny miesiąc: {{ number_format($currentMonth, 2) }} zł<br>
                                Poprzedni miesiąc: {{ number_format($lastMonth, 2) }} zł
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Szybkie akcje -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Szybkie akcje</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4">
                <a href="{{ route('admin.users.create') }}" class="bg-steel-blue-50 hover:bg-steel-blue-100 border border-steel-blue-200 rounded-lg p-4 flex items-center transition-colors">
                    <div class="p-2 rounded-full bg-steel-blue-200 text-steel-blue-700 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <span>Dodaj użytkownika</span>
                </a>
                <a href="{{ route('admin.subscriptions.create') }}" class="bg-steel-blue-50 hover:bg-steel-blue-100 border border-steel-blue-200 rounded-lg p-4 flex items-center transition-colors">
                    <div class="p-2 rounded-full bg-steel-blue-200 text-steel-blue-700 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span>Nowa subskrypcja</span>
                </a>
                <a href="{{ route('admin.system.logs') }}" class="bg-steel-blue-50 hover:bg-steel-blue-100 border border-steel-blue-200 rounded-lg p-4 flex items-center transition-colors">
                    <div class="p-2 rounded-full bg-steel-blue-200 text-steel-blue-700 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span>Przeglądaj logi</span>
                </a>
                <a href="{{ route('admin.system.backup') }}" class="bg-steel-blue-50 hover:bg-steel-blue-100 border border-steel-blue-200 rounded-lg p-4 flex items-center transition-colors">
                    <div class="p-2 rounded-full bg-steel-blue-200 text-steel-blue-700 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                    </div>
                    <span>Kopia zapasowa</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Najnowsi użytkownicy -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-base font-medium text-gray-900">Najnowsi użytkownicy</h2>
                    <a href="{{ route('admin.users.index') }}" class="text-xs text-steel-blue-600 hover:text-steel-blue-800">
                        Zobacz wszystkich
                    </a>
                </div>
                <div class="divide-y divide-gray-200 max-h-80 overflow-y-auto">
                    @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                        <div class="p-3 hover:bg-gray-50">
                            <div class="flex items-center">
                                <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=6366f1&color=fff' }}" alt="Avatar" class="h-8 w-8 rounded-full">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Dołączył: {{ $user->created_at->format('d.m.Y') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Ostatnie logowania -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-base font-medium text-gray-900">Ostatnie logowania</h2>
                    <a href="{{ route('admin.system.login-history') }}" class="text-xs text-steel-blue-600 hover:text-steel-blue-800">
                        Pełna historia
                    </a>
                </div>
                <div class="divide-y divide-gray-200 max-h-80 overflow-y-auto">
                    @foreach(\App\Models\User::whereNotNull('last_login_at')->orderByDesc('last_login_at')->take(5)->get() as $user)
                        <div class="p-3 hover:bg-gray-50">
                            <div class="flex items-center">
                                <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=6366f1&color=fff' }}" alt="Avatar" class="h-8 w-8 rounded-full">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Ostatnie logowanie: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Brak' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Informacje o systemie -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-base font-medium text-gray-900">Informacje o systemie</h2>
                    <a href="{{ route('admin.system.info') }}" class="text-xs text-steel-blue-600 hover:text-steel-blue-800">
                        Szczegóły
                    </a>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <p class="text-sm font-medium text-gray-700">PHP Version</p>
                        <p class="text-sm text-gray-500">{{ phpversion() }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Laravel Version</p>
                        <p class="text-sm text-gray-500">{{ app()->version() }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Środowisko</p>
                        <p class="text-sm text-gray-500">{{ config('app.env') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Pamięć podręczna</p>
                        <p class="text-sm text-gray-500">{{ config('cache.default') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Ostatnia aktualizacja</p>
                        <p class="text-sm text-gray-500">{{ \Illuminate\Support\Facades\File::exists(base_path('.git/FETCH_HEAD')) ? date('d.m.Y H:i', filemtime(base_path('.git/FETCH_HEAD'))) : 'Brak informacji' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generowanie danych dla wykresu na podstawie rzeczywistych danych
            @php
                $months = [];
                $revenueData = [];
                
                // Pobierz dane za ostatnie 6 miesięcy
                for($i = 5; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $monthLabel = $date->format('M Y');
                    $months[] = $monthLabel;
                    
                    $revenue = \App\Models\Subscription::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->sum('price');
                        
                    $revenueData[] = $revenue;
                }
            @endphp
            
            const ctx = document.getElementById('subscriptionChart').getContext('2d');
            const monthlyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($months),
                    datasets: [{
                        label: 'Przychód (zł)',
                        data: @json($revenueData),
                        backgroundColor: 'rgba(84, 110, 149, 0.7)',
                        borderColor: 'rgba(84, 110, 149, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' zł';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw.toLocaleString() + ' zł';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-admin-layout> 