@php
    $toPoints = function (array $values): string {
        $max = max(1, max($values));
        $count = max(1, count($values) - 1);

        return collect($values)
            ->map(function ($value, $i) use ($max, $count) {
                $x = round(($i / $count) * 100, 2);
                $y = round(100 - (($value / $max) * 100), 2);
                return $x . ',' . $y;
            })
            ->implode(' ');
    };

    $normalizeStatus = function (array $rows): array {
        $total = max(1, collect($rows)->sum('value'));
        return collect($rows)
            ->map(fn($row) => [
                'label' => $row['label'],
                'value' => $row['value'],
                'pct' => round(($row['value'] / $total) * 100, 1),
            ])
            ->all();
    };

    $subscriptionBars = $normalizeStatus($subscriptionStatus ?? []);
    $paymentBars = $normalizeStatus($paymentStatus ?? []);
@endphp

<div class="space-y-6">
    @if($billingWarning)
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-700/40 dark:bg-amber-900/20 dark:text-amber-300">
            {{ $billingWarning }}
        </div>
    @endif

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <article class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ t('Users') }}</p>
            <p class="mt-2 text-2xl font-black text-zinc-900 dark:text-zinc-100">{{ number_format((int) ($kpis['users'] ?? 0), 0, ',', '.') }}</p>
        </article>
        <article class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ t('Collections') }}</p>
            <p class="mt-2 text-2xl font-black text-zinc-900 dark:text-zinc-100">{{ number_format((int) ($kpis['collections'] ?? 0), 0, ',', '.') }}</p>
        </article>
        <article class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ t('Items') }}</p>
            <p class="mt-2 text-2xl font-black text-zinc-900 dark:text-zinc-100">{{ number_format((int) ($kpis['items'] ?? 0), 0, ',', '.') }}</p>
        </article>
        <article class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ t('Downloads') }}</p>
            <p class="mt-2 text-2xl font-black text-zinc-900 dark:text-zinc-100">{{ number_format((int) ($kpis['downloads'] ?? 0), 0, ',', '.') }}</p>
        </article>
        <article class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ t('Approved revenue') }}</p>
            <p class="mt-2 text-2xl font-black text-zinc-900 dark:text-zinc-100">R$ {{ number_format((float) ($kpis['revenue_approved'] ?? 0), 2, ',', '.') }}</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <article class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200">{{ t('7-day trend') }}</h2>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $trendLabels[0] ?? '' }} {{ t('to') }} {{ $trendLabels[count($trendLabels)-1] ?? '' }}</span>
            </div>

            <div class="space-y-4">
                <div>
                    <div class="mb-1 flex items-center justify-between text-xs">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">{{ t('Users') }}</span>
                        <span class="text-zinc-500 dark:text-zinc-400">{{ array_sum($trend['users'] ?? []) }} {{ t('new') }}</span>
                    </div>
                    <svg viewBox="0 0 100 100" class="h-14 w-full overflow-visible">
                        <polyline fill="none" stroke="currentColor" stroke-width="2.4" class="text-sky-500" points="{{ $toPoints($trend['users'] ?? array_fill(0, 7, 0)) }}" />
                    </svg>
                </div>

                <div>
                    <div class="mb-1 flex items-center justify-between text-xs">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">{{ t('Collections') }}</span>
                        <span class="text-zinc-500 dark:text-zinc-400">{{ array_sum($trend['collections'] ?? []) }} {{ t('new') }}</span>
                    </div>
                    <svg viewBox="0 0 100 100" class="h-14 w-full overflow-visible">
                        <polyline fill="none" stroke="currentColor" stroke-width="2.4" class="text-indigo-500" points="{{ $toPoints($trend['collections'] ?? array_fill(0, 7, 0)) }}" />
                    </svg>
                </div>

                <div>
                    <div class="mb-1 flex items-center justify-between text-xs">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">{{ t('Downloads') }}</span>
                        <span class="text-zinc-500 dark:text-zinc-400">{{ array_sum($trend['downloads'] ?? []) }} {{ t('in the period') }}</span>
                    </div>
                    <svg viewBox="0 0 100 100" class="h-14 w-full overflow-visible">
                        <polyline fill="none" stroke="currentColor" stroke-width="2.4" class="text-emerald-500" points="{{ $toPoints($trend['downloads'] ?? array_fill(0, 7, 0)) }}" />
                    </svg>
                </div>

                <div>
                    <div class="mb-1 flex items-center justify-between text-xs">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-200">{{ t('Payments') }}</span>
                        <span class="text-zinc-500 dark:text-zinc-400">{{ array_sum($trend['payments'] ?? []) }} {{ t('in the period') }}</span>
                    </div>
                    <svg viewBox="0 0 100 100" class="h-14 w-full overflow-visible">
                        <polyline fill="none" stroke="currentColor" stroke-width="2.4" class="text-amber-500" points="{{ $toPoints($trend['payments'] ?? array_fill(0, 7, 0)) }}" />
                    </svg>
                </div>
            </div>
        </article>

        <article class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200">{{ t('Status distribution') }}</h2>

            <div class="space-y-5">
                <div>
                    <p class="mb-2 text-xs font-semibold text-zinc-600 dark:text-zinc-300">{{ t('Subscriptions') }}</p>
                    <div class="space-y-2">
                        @forelse($subscriptionBars as $row)
                            <div>
                                <div class="mb-1 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                                    <span>{{ $row['label'] }}</span>
                                    <span>{{ $row['value'] }} ({{ $row['pct'] }}%)</span>
                                </div>
                                <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                                    <div class="h-2 rounded-full bg-sky-500" style="width: {{ $row['pct'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('No subscription data.') }}</p>
                        @endforelse
                    </div>
                </div>

                <div>
                    <p class="mb-2 text-xs font-semibold text-zinc-600 dark:text-zinc-300">{{ t('Payments') }}</p>
                    <div class="space-y-2">
                        @forelse($paymentBars as $row)
                            <div>
                                <div class="mb-1 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                                    <span>{{ $row['label'] }}</span>
                                    <span>{{ $row['value'] }} ({{ $row['pct'] }}%)</span>
                                </div>
                                <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                                    <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $row['pct'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('No payment data.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <article class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200">{{ t('Top Plans') }}</h2>
            <div class="overflow-hidden rounded-lg border border-zinc-100 dark:border-zinc-800">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/40">
                        <tr class="text-left text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            <th class="px-3 py-2">{{ t('Plan') }}</th>
                            <th class="px-3 py-2">{{ t('Price') }}</th>
                            <th class="px-3 py-2">{{ t('Active') }}</th>
                            <th class="px-3 py-2">{{ t('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topPlans as $plan)
                            <tr class="border-t border-zinc-100 dark:border-zinc-800">
                                <td class="px-3 py-2 font-medium text-zinc-800 dark:text-zinc-200">{{ $plan['name'] }}</td>
                                <td class="px-3 py-2 text-zinc-600 dark:text-zinc-300">R$ {{ number_format((float) $plan['price'], 2, ',', '.') }}</td>
                                <td class="px-3 py-2 text-zinc-600 dark:text-zinc-300">{{ $plan['active_count'] }}</td>
                                <td class="px-3 py-2 text-zinc-600 dark:text-zinc-300">{{ $plan['subscriptions_count'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-5 text-center text-xs text-zinc-500 dark:text-zinc-400">{{ t('No plan data.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-200">{{ t('Taxonomy summary') }}</h2>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-lg border border-zinc-100 bg-zinc-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Categories') }}</p>
                    <p class="mt-1 text-xl font-black text-zinc-900 dark:text-zinc-100">{{ number_format((int) ($kpis['categories'] ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-zinc-100 bg-zinc-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Tags') }}</p>
                    <p class="mt-1 text-xl font-black text-zinc-900 dark:text-zinc-100">{{ number_format((int) ($kpis['tags'] ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-zinc-100 bg-zinc-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Subscriptions') }}</p>
                    <p class="mt-1 text-xl font-black text-zinc-900 dark:text-zinc-100">{{ number_format((int) ($kpis['subscriptions'] ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-zinc-100 bg-zinc-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Payments') }}</p>
                    <p class="mt-1 text-xl font-black text-zinc-900 dark:text-zinc-100">{{ number_format((int) ($kpis['payments'] ?? 0), 0, ',', '.') }}</p>
                </div>
            </div>
        </article>
    </section>
</div>
