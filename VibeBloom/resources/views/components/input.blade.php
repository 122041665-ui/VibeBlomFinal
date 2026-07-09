@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 focus:border-blue-400 focus:ring-blue-200 dark:focus:border-blue-500 dark:focus:ring-blue-500/30 rounded-xl shadow-sm']) !!}>
