<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-white/90 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl font-bold text-xs text-gray-700 dark:text-slate-200 uppercase tracking-wide shadow-sm hover:bg-blue-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 disabled:opacity-25 transition']) }}>
    {{ $slot }}
</button>
