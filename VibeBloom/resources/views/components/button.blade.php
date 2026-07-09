<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-wide shadow-sm hover:bg-blue-700 focus:bg-blue-700 active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 disabled:opacity-50 transition']) }}>
    {{ $slot }}
</button>
