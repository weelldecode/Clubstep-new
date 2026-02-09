<div class="min-h-screen px-4 py-8 md:px-8">
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp .6s ease-out both; }
        .delay-1 { animation-delay: .08s; }
        .delay-2 { animation-delay: .16s; }
    </style>

    <div class="mx-auto max-w-6xl">
        <div class="mb-8 flex items-center justify-between fade-up">
            <a href="{{ route('home') }}" class="inline-flex items-center" wire:navigate>
                <x-app-logo />
            </a>
            <span class="rounded-full bg-accent/10 px-3 py-1 text-xs font-semibold text-accent ring-1 ring-accent/20">
                {{ t('Secure checkout') }}
            </span>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <aside class="lg:col-span-4 fade-up delay-1">
                <div class="space-y-4 lg:sticky lg:top-8">
                    <article class="rounded-2xl bg-white p-5 shadow-[0_20px_55px_-45px_rgba(0,0,0,0.25)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">{{ t('Selected plan') }}</p>
                        <h1 class="mt-2 text-2xl font-black tracking-tight text-zinc-900">{{ $plan->name }}</h1>

                        <div class="mt-4 flex items-end gap-2">
                            <span class="text-4xl font-black tracking-tight text-accent">R$ {{ $plan->price }}</span>
                            <span class="pb-1 text-sm font-medium text-zinc-500">{{ t('per month') }}</span>
                        </div>
                    </article>

                    <article class="rounded-2xl bg-white p-5 shadow-[0_20px_55px_-45px_rgba(0,0,0,0.25)]">
                        <h2 class="text-sm font-bold uppercase tracking-[0.14em] text-zinc-500">{{ t('Summary') }}</h2>

                        <div class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between text-zinc-600">
                                <span>{{ t('Plan') }}</span>
                                <span class="font-semibold text-zinc-800">{{ $plan->name }}</span>
                            </div>
                            <div class="flex items-center justify-between text-zinc-600">
                                <span>{{ t('Subtotal') }}</span>
                                <span class="font-semibold text-zinc-800">R$ {{ $plan->price }}</span>
                            </div>
                            <div class="flex items-center justify-between text-zinc-600">
                                <span>{{ t('Fee') }}</span>
                                <span class="font-semibold text-zinc-800">R$ 0,00</span>
                            </div>
                        </div>

                        <div class="my-4 h-px bg-zinc-200"></div>

                        <div class="flex items-center justify-between">
                            <span class="text-base font-bold text-zinc-800">{{ t('Total') }}</span>
                            <span class="text-xl font-black tracking-tight text-accent">R$ {{ $plan->price }}</span>
                        </div>
                    </article>
                </div>
            </aside>

            <main class="lg:col-span-8 fade-up delay-2">
                <section class="rounded-2xl bg-white p-4 shadow-[0_24px_60px_-46px_rgba(0,0,0,0.25)] md:p-6">
                    <div class="mb-5">
                        <h2 class="text-xl font-black tracking-tight text-zinc-900">{{ t('Payment') }}</h2>
                        <p class="mt-1 text-sm text-zinc-500">
                            {{ t('Complete the details to finish your subscription.') }}
                        </p>
                    </div>

                    <div
                        id="checkout-data"
                        data-plan-price="{{ $plan->price }}"
                        data-plan-id="{{ $plan->id }}"
                        data-csrf="{{ csrf_token() }}"
                        data-process-url="{{ route('checkout.process') }}"
                        data-public-key="{{ env('MP_PUBLIC_KEY') }}"
                    ></div>

                    <div id="form-checkout"></div>
                    <div id="status-screen-container" style="display: none;"></div>
                </section>
            </main>
        </div>
    </div>

    <input type="hidden" id="plan_price" value="{{ $plan->price }}">
    <input type="hidden" id="plan_id" value="{{ $plan->id }}">
</div>
 
