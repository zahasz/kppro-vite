<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">
                        {{ __('Faktury') }}
                    </x-nav-link>
                    <x-nav-link :href="route('contractors.index')" :active="request()->routeIs('contractors.*')">
                        {{ __('Kontrahenci') }}
                    </x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        {{ __('Produkty') }}
                    </x-nav-link>
                    <x-nav-link :href="route('finances.index')" :active="request()->routeIs('finances.*')">
                        {{ __('Finanse') }}
                    </x-nav-link>
                    <x-nav-link :href="route('finances.budget.index')" :active="request()->routeIs('finances.budget.*')">
                        {{ __('Budżet') }}
                    </x-nav-link>
                    <x-nav-link :href="route('warehouse.index')" :active="request()->routeIs('warehouse.*')">
                        {{ __('Magazyn') }}
                    </x-nav-link>
                    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
                        {{ __('Zadania') }}
                    </x-nav-link>
                    <x-nav-link :href="route('contracts.index')" :active="request()->routeIs('contracts.*')">
                        {{ __('Umowy') }}
                    </x-nav-link>
                    <x-nav-link :href="route('estimates.index')" :active="request()->routeIs('estimates.*')">
                        {{ __('Kosztorysy') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                {{ Auth::user()->name }}
                                <span class="ml-2 px-2 py-0.5 text-xs rounded bg-blue-100 text-blue-800">Premium</span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Mój profil') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit') . '#dane-uzytkownika'">
                            {{ __('Dane użytkownika') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit') . '#dane-firmy'">
                            {{ __('Dane firmy') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit') . '#dane-bankowe'">
                            {{ __('Dane bankowe') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit') . '#ustawienia-faktur'">
                            {{ __('Ustawienia faktur') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit') . '#subskrypcja'">
                            {{ __('Subskrypcja') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit') . '#bezpieczenstwo'">
                            {{ __('Bezpieczeństwo') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Wyloguj') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">
                {{ __('Faktury') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contractors.index')" :active="request()->routeIs('contractors.*')">
                {{ __('Kontrahenci') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                {{ __('Produkty') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('finances.index')" :active="request()->routeIs('finances.*')">
                {{ __('Finanse') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('finances.budget.index')" :active="request()->routeIs('finances.budget.*')">
                {{ __('Budżet') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('warehouse.index')" :active="request()->routeIs('warehouse.*')">
                {{ __('Magazyn') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
                {{ __('Zadania') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contracts.index')" :active="request()->routeIs('contracts.*')">
                {{ __('Umowy') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('estimates.index')" :active="request()->routeIs('estimates.*')">
                {{ __('Kosztorysy') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">
                    <div class="flex items-center">
                        {{ Auth::user()->name }}
                        <span class="ml-2 px-2 py-0.5 text-xs rounded bg-blue-100 text-blue-800">Premium</span>
                    </div>
                </div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Mój profil') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit') . '#dane-uzytkownika'" class="pl-6">
                    {{ __('Dane użytkownika') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit') . '#dane-firmy'" class="pl-6">
                    {{ __('Dane firmy') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit') . '#dane-bankowe'" class="pl-6">
                    {{ __('Dane bankowe') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit') . '#ustawienia-faktur'" class="pl-6">
                    {{ __('Ustawienia faktur') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit') . '#subskrypcja'" class="pl-6">
                    {{ __('Subskrypcja') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit') . '#bezpieczenstwo'" class="pl-6">
                    {{ __('Bezpieczeństwo') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Wyloguj') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        updateActiveMenuSection();
    });
    
    // Obsługa zmiany hasha przy przewijaniu
    window.addEventListener('hashchange', function() {
        updateActiveMenuSection();
    });
    
    function updateActiveMenuSection() {
        if (window.location.href.includes('profile.edit')) {
            const hash = window.location.hash || '#dane-uzytkownika';
            
            // Znajdź wszystkie linki w dropdown menu użytkownika
            const dropdownLinks = document.querySelectorAll('.dropdown-link');
            
            // Usuń aktywne klasy z wszystkich linków dropdown
            dropdownLinks.forEach(link => {
                link.classList.remove('bg-gray-100', 'active');
            });
            
            // Znajdź pasujący link w dropdown
            dropdownLinks.forEach(link => {
                if (link.href && link.href.includes(hash)) {
                    link.classList.add('bg-gray-100', 'active');
                    console.log('Aktywny link dropdown znaleziony i podświetlony:', link.href);
                }
            });
        }
    }
</script>
