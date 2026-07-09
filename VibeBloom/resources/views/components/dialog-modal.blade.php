@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-5">
        <div class="text-lg font-extrabold text-gray-900 dark:text-slate-100">
            {{ $title }}
        </div>

        <div class="mt-4 text-sm text-gray-600 dark:text-slate-400">
            {{ $content }}
        </div>
    </div>

    <div class="flex flex-row justify-end px-6 py-4 bg-slate-50/90 dark:bg-slate-950/50 text-end border-t border-gray-100 dark:border-slate-800">
        {{ $footer }}
    </div>
</x-modal>
