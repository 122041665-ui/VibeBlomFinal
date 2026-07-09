@props(['submit'])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="px-4 py-5 bg-white/92 dark:bg-slate-900/92 sm:p-6 shadow-sm border border-gray-100 dark:border-slate-800 backdrop-blur {{ isset($actions) ? 'rounded-t-[24px]' : 'rounded-[24px]' }}">
                <div class="grid grid-cols-6 gap-6">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="flex items-center justify-end px-4 py-3 bg-slate-50/90 dark:bg-slate-950/50 text-end sm:px-6 shadow-sm border-x border-b border-gray-100 dark:border-slate-800 rounded-b-[24px]">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
