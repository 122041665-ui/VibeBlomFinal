<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <div class="px-4 py-5 sm:p-6 bg-white/92 dark:bg-slate-900/92 shadow-sm rounded-[24px] border border-gray-100 dark:border-slate-800 backdrop-blur">
            {{ $content }}
        </div>
    </div>
</div>
