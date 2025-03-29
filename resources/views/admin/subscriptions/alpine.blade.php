<x-admin-layout>
    <x-slot name="header">
        Zarządzanie planami subskrypcji (Alpine.js)
    </x-slot>

<div x-data="subscriptionPlans()" class="space-y-6">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Plany subskrypcji</h2>
            <a href="{{ route('admin.subscriptions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Dodaj nowy plan
            </a>
        </div>

        <!-- Filtrowanie -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-wrap gap-3">
                <button @click="filterType = 'all'" :class="{ 'bg-blue-100 text-blue-800 border-blue-300': filterType === 'all' }" class="px-3 py-1 border rounded-md text-sm font-medium text-gray-700">
                    Wszystkie
                </button>
                <button @click="filterType = 'manual'" :class="{ 'bg-orange-100 text-orange-800 border-orange-300': filterType === 'manual' }" class="px-3 py-1 border rounded-md text-sm font-medium text-gray-700">
                    Ręczne
                </button>
                <button @click="filterType = 'automatic'" :class="{ 'bg-green-100 text-green-800 border-green-300': filterType === 'automatic' }" class="px-3 py-1 border rounded-md text-sm font-medium text-gray-700">
                    Automatyczne
                </button>
                <button @click="filterType = 'both'" :class="{ 'bg-purple-100 text-purple-800 border-purple-300': filterType === 'both' }" class="px-3 py-1 border rounded-md text-sm font-medium text-gray-700">
                    Obie opcje
                </button>
            </div>
        </div>

        <!-- Tabela planów -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa planu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Okres</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Użytkownicy</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="plan in filteredPlans" :key="plan.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="plan.name"></div>
                                        <div class="text-sm text-gray-500" x-text="plan.description"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="formatPrice(plan.price) + ' PLN'"></div>
                                <div class="text-sm text-gray-500" x-text="formatInterval(plan.interval)"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="formatIntervalName(plan.interval)"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                      :class="plan.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    <span x-text="plan.is_active ? 'Aktywny' : 'Nieaktywny'"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                      :class="getSubscriptionTypeClass(plan.subscription_type)">
                                    <span x-text="formatSubscriptionType(plan.subscription_type)"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span x-text="plan.subscriptions_count || 0"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a :href="'{{ route('admin.subscriptions.edit', '') }}/' + plan.id" class="text-indigo-600 hover:text-indigo-900 mr-3">Edytuj</a>
                                <button @click="confirmDelete(plan)" class="text-red-600 hover:text-red-900">Usuń</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Statystyki -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Statystyki subskrypcji</h2>
        </div>
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Aktywne subskrypcje</h3>
                <div class="text-3xl font-bold text-indigo-600" x-text="statistics.active_subscriptions || 0"></div>
                <div class="text-sm text-gray-500 mt-1">
                    <span x-show="statistics.sub_monthly_change > 0" class="text-green-600" x-text="'+' + statistics.sub_monthly_change + '%'"></span>
                    <span x-show="statistics.sub_monthly_change < 0" class="text-red-600" x-text="statistics.sub_monthly_change + '%'"></span>
                    <span x-show="statistics.sub_monthly_change == 0">0%</span>
                    <span> w porównaniu do poprzedniego miesiąca</span>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Przychód miesięczny</h3>
                <div class="text-3xl font-bold text-green-600" x-text="formatPrice(statistics.month_subscriptions_value || 0) + ' PLN'"></div>
                <div class="text-sm text-gray-500 mt-1">
                    <span x-show="statistics.revenue_monthly_change > 0" class="text-green-600" x-text="'+' + statistics.revenue_monthly_change + '%'"></span>
                    <span x-show="statistics.revenue_monthly_change < 0" class="text-red-600" x-text="statistics.revenue_monthly_change + '%'"></span>
                    <span x-show="statistics.revenue_monthly_change == 0">0%</span>
                    <span> w porównaniu do poprzedniego miesiąca</span>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Średnia wartość subskrypcji</h3>
                <div class="text-3xl font-bold text-blue-600" x-text="formatPrice(calculateAvgValue()) + ' PLN'"></div>
                <div class="text-sm text-gray-500 mt-1">
                    <span x-show="statistics.avg_value_monthly_change > 0" class="text-green-600" x-text="'+' + statistics.avg_value_monthly_change + '%'"></span>
                    <span x-show="statistics.avg_value_monthly_change < 0" class="text-red-600" x-text="statistics.avg_value_monthly_change + '%'"></span>
                    <span x-show="statistics.avg_value_monthly_change == 0">0%</span>
                    <span> w porównaniu do poprzedniego miesiąca</span>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Subskrypcje ręczne</h3>
                <div class="text-3xl font-bold text-orange-600" x-text="statistics.manual_subscriptions || 0"></div>
                <div class="text-sm text-gray-500 mt-1" x-text="(statistics.manual_percentage || 0) + '% wszystkich subskrypcji'"></div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Subskrypcje automatyczne</h3>
                <div class="text-3xl font-bold text-blue-600" x-text="statistics.automatic_subscriptions || 0"></div>
                <div class="text-sm text-gray-500 mt-1" x-text="(statistics.automatic_percentage || 0) + '% wszystkich subskrypcji'"></div>
            </div>
        </div>
    </div>
    
    <!-- Modal usuwania -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-10 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Usuń plan subskrypcji
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Czy na pewno chcesz usunąć ten plan subskrypcji? Ta operacja jest nieodwracalna.
                                </p>
                                <p class="mt-2 text-sm font-semibold" x-text="planToDelete ? planToDelete.name : ''"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="deletePlan()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Usuń
                    </button>
                    <button @click="showDeleteModal = false; planToDelete = null" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Anuluj
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function subscriptionPlans() {
        return {
            plans: @json($plans),
            statistics: @json($stats),
            filterType: 'all',
            showDeleteModal: false,
            planToDelete: null,
            
            get filteredPlans() {
                if (this.filterType === 'all') {
                    return this.plans;
                }
                
                return this.plans.filter(plan => plan.subscription_type === this.filterType);
            },
            
            formatPrice(price) {
                return parseFloat(price).toFixed(2).replace('.', ',');
            },
            
            formatInterval(interval) {
                switch (interval) {
                    case 'monthly': return 'miesięcznie';
                    case 'quarterly': return 'kwartalnie';
                    case 'biannually': return 'półrocznie';
                    case 'annually': return 'rocznie';
                    case 'lifetime': return 'jednorazowo';
                    default: return interval;
                }
            },
            
            formatIntervalName(interval) {
                switch (interval) {
                    case 'monthly': return 'Miesięczny';
                    case 'quarterly': return 'Kwartalny';
                    case 'biannually': return 'Półroczny';
                    case 'annually': return 'Roczny';
                    case 'lifetime': return 'Bezterminowy';
                    default: return interval;
                }
            },
            
            formatSubscriptionType(type) {
                switch (type) {
                    case 'manual': return 'Ręczna';
                    case 'automatic': return 'Automatyczna';
                    case 'both': return 'Oba typy';
                    default: return type;
                }
            },
            
            getSubscriptionTypeClass(type) {
                switch (type) {
                    case 'manual': return 'bg-orange-100 text-orange-800';
                    case 'automatic': return 'bg-blue-100 text-blue-800';
                    case 'both': return 'bg-purple-100 text-purple-800';
                    default: return 'bg-gray-100 text-gray-800';
                }
            },
            
            calculateAvgValue() {
                if (!this.statistics.active_subscriptions || this.statistics.active_subscriptions === 0) {
                    return 0;
                }
                
                return this.statistics.active_subscriptions_value / this.statistics.active_subscriptions;
            },
            
            confirmDelete(plan) {
                this.planToDelete = plan;
                this.showDeleteModal = true;
            },
            
            deletePlan() {
                if (!this.planToDelete) {
                    return;
                }
                
                fetch(`/admin/subscriptions/${this.planToDelete.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Usunięcie planu z listy
                        this.plans = this.plans.filter(p => p.id !== this.planToDelete.id);
                        this.showDeleteModal = false;
                        this.planToDelete = null;
                        
                        // Odświeżenie statystyk
                        this.statistics.active_subscriptions -= 1;
                    } else {
                        alert(data.message || 'Wystąpił błąd podczas usuwania planu.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Wystąpił błąd podczas usuwania planu. Sprawdź konsolę, aby uzyskać więcej informacji.');
                });
            }
        };
    }
</script>
@endpush

</x-admin-layout> 