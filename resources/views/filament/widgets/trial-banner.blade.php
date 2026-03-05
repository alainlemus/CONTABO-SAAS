<div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 flex items-center justify-between gap-4 dark:border-amber-700 dark:bg-amber-950">
    <div class="flex items-center gap-3">
        <div class="shrink-0 text-amber-500">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">
                Período de prueba activo
            </p>
            <p class="text-sm text-amber-700 dark:text-amber-300">
                Te quedan <strong>{{ $this->getDaysLeft() }} {{ $this->getDaysLeft() === 1 ? 'día' : 'días' }}</strong>
                de prueba gratuita — vence el {{ $this->getTrialEndsAt() }}.
            </p>
        </div>
    </div>
    <a
        href="/subscription"
        class="shrink-0 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors"
    >
        Activar suscripción
    </a>
</div>
