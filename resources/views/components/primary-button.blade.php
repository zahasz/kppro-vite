<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-steel-blue-600 border border-steel-blue-700 rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:bg-steel-blue-700 hover:shadow-lg focus:bg-steel-blue-700 active:bg-steel-blue-800 focus:outline-none focus:ring-2 focus:ring-steel-blue-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
