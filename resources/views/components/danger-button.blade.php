<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-red-500 border border-red-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:bg-red-600 hover:shadow-lg active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
