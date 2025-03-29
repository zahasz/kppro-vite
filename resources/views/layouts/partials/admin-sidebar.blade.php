<x-menu.admin-menu />

<div class="fixed bottom-0 left-0 bg-[#384057] border-t border-[#44546A]/40 rounded-t-xl mx-2" 
     style="width: calc(100% - 16px);"
     :class="{'w-60': sidebarOpen, 'w-12': !sidebarOpen}">
    <!-- Przycisk powrotu do aplikacji -->
    <a href="{{ route('dashboard') }}" 
       class="flex items-center p-3 text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200 rounded-t-xl">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/5">
            <i class="fas fa-arrow-left"></i>
        </div>
        <span class="ml-3 text-sm font-medium" x-show="sidebarOpen">Powrót do aplikacji</span>
    </a>

    <!-- Przycisk wylogowania -->
    <form method="POST" action="{{ route('logout') }}" class="block">
        @csrf
        <button type="submit" class="flex items-center p-3 w-full text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/5">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <span class="ml-3 text-sm font-medium text-left" x-show="sidebarOpen">Wyloguj się</span>
        </button>
    </form>
</div> 