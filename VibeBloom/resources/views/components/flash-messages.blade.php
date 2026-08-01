@if (session('success'))
    <div role="status" class="mb-5 flex items-start gap-3 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm dark:border-green-500/30 dark:bg-green-500/10 dark:text-green-300">
        <span aria-hidden="true" class="font-bold">✓</span>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div role="alert" class="mb-5 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300">
        <span aria-hidden="true" class="font-bold">!</span>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if ($errors->any())
    <div role="alert" class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300">
        <p class="font-bold">Revisa la información antes de continuar:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
