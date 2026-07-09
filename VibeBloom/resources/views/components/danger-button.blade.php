<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-red-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-wide shadow-sm hover:bg-red-700 active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-red-200 dark:focus:ring-red-500/30 transition']) }}>
    {{ $slot }}
</button>
