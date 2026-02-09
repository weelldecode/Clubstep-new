@php
    $planName = $subscription?->plan?->name ?? t('No plan');
    $subscriptionStatus = $subscription?->status ? t(ucfirst($subscription->status)) : t('Undefined');
@endphp

<section class="container mx-auto w-full px-4 md:px-8">
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp .55s ease-out both; }
        .delay-1 { animation-delay: .08s; }
        .delay-2 { animation-delay: .16s; }
    </style>

    <header class="mt-6 mb-8 fade-up">
        <livewire:components.breadcrumb />
        <h1 class="mt-2 text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
            {{ t('Billing and Invoicing') }}
        </h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            {{ t('View your plan information or switch plans according to your needs.') }}
        </p>
        <flux:separator class="mt-6" variant="subtle" />
    </header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <aside class="lg:col-span-4 fade-up delay-1">
            <div class="space-y-4 lg:sticky lg:top-24">
                <article class="rounded-2xl border border-zinc-200/70 bg-white/80 p-5 shadow-[0_20px_50px_-40px_rgba(0,0,0,0.45)] backdrop-blur dark:border-zinc-800/80 dark:bg-zinc-950/80">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">{{ t('Current plan') }}</p>
                    <h2 class="mt-2 text-2xl font-black tracking-tight text-accent">{{ $planName }}</h2>

                    <div class="mt-5">
                        <div class="mb-2 flex items-center justify-between">
                            <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ t('Downloads today') }}</p>
                            <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                {{ $downloadsHoje }} / {{ $limiteDiario }}
                            </p>
                        </div>

                        <div class="h-3 w-full rounded-full bg-zinc-200 dark:bg-zinc-800">
                            <div
                                class="h-3 rounded-full bg-accent transition-all duration-500"
                                style="width: {{ $percentualDownloads }}%;"
                            ></div>
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-zinc-200/70 bg-white/80 p-5 shadow-[0_20px_50px_-40px_rgba(0,0,0,0.45)] backdrop-blur dark:border-zinc-800/80 dark:bg-zinc-950/80">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">{{ t('Subscription') }}</p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                            @if(($subscription->status ?? null) === 'active') bg-emerald-500/10 text-emerald-600 ring-1 ring-emerald-500/20
                            @elseif(($subscription->status ?? null) === 'pending') bg-amber-500/10 text-amber-600 ring-1 ring-amber-500/20
                            @else bg-zinc-500/10 text-zinc-600 ring-1 ring-zinc-500/20 dark:text-zinc-300
                            @endif">
                            {{ $subscriptionStatus }}
                        </span>
                    </div>

                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-zinc-500 dark:text-zinc-400">{{ t('Start') }}</dt>
                            <dd class="font-medium text-zinc-700 dark:text-zinc-200">{{ $inicio ?? '-' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-zinc-500 dark:text-zinc-400">{{ t('Due date') }}</dt>
                            <dd class="font-medium text-zinc-700 dark:text-zinc-200">{{ $vencimento ?? '-' }}</dd>
                        </div>
                    </dl>
                </article>
            </div>
        </aside>

        <main class="lg:col-span-8 fade-up delay-2">
            <section class="rounded-2xl border border-zinc-200/70 bg-white/80 p-5 shadow-[0_20px_50px_-40px_rgba(0,0,0,0.45)] backdrop-blur dark:border-zinc-800/80 dark:bg-zinc-950/80">
                <div class="mb-5">
                    <h3 class="text-xl font-black tracking-tight text-zinc-900 dark:text-white">{{ t('Recent invoices') }}</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ t('See your recent invoices.') }}</p>
                </div>

                <div class="space-y-3">
                    @forelse($faturas as $fatura)
                        <article class="flex flex-col gap-4 rounded-xl border border-zinc-200/70 bg-zinc-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-900/70 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="line-clamp-1 text-base font-bold text-zinc-800 dark:text-zinc-100">
                                    {{ t('Plan') }}: {{ $fatura->subscription?->plan?->name ?? '-' }}
                                </p>
                                <p class="mt-1 text-sm font-semibold text-zinc-600 dark:text-zinc-300">
                                    R$ {{ number_format($fatura->amount, 2, ',', '.') }}
                                </p>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $fatura->paid_at?->format('d/m/Y') ?? $fatura->created_at->format('d/m/Y') }}
                                </p>
                            </div>

                            <div>
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold
                                    @if($fatura->status === 'paid') bg-emerald-500/10 text-emerald-600 ring-1 ring-emerald-500/20
                                    @elseif($fatura->status === 'pending') bg-amber-500/10 text-amber-600 ring-1 ring-amber-500/20
                                    @else bg-red-500/10 text-red-600 ring-1 ring-red-500/20
                                    @endif">
                                    {{ t(ucfirst($fatura->status)) }}
                                </span>
                            </div>
                        </article>
                    @empty
                        <div class="flex flex-col items-center justify-center rounded-xl border border-zinc-200/70 bg-zinc-50/80 py-16 text-center dark:border-zinc-800 dark:bg-zinc-900/60">
                            <flux:icon name="gallery-vertical-end" class="mb-3 h-10 w-10 text-accent" />
                            <p class="text-lg font-semibold text-zinc-700 dark:text-zinc-100">{{ t('No invoice generated.') }}</p>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ t('You do not have any available invoices yet.') }}</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</section>
