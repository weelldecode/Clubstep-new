<div class="relative px-4 md:px-8">
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp .6s ease-out both; }
        .delay-1 { animation-delay: .08s; }
        .delay-2 { animation-delay: .16s; }
        .delay-3 { animation-delay: .24s; }
    </style>

    <div class="mx-auto mt-14 max-w-6xl">
        <section class="fade-up">
            <h1 class="mx-auto max-w-4xl text-center text-4xl font-black tracking-tight text-zinc-900 dark:text-white md:text-6xl">
                {{ t('Unlimited creative resources to accelerate your flow.') }}
            </h1>
            <p class="mx-auto mt-4 max-w-3xl text-center text-base text-zinc-500 dark:text-zinc-300 md:text-lg">
                {{ t('Download premium collections and templates with great value. Simple plans, no complexity.') }}
            </p>
        </section>

        <section class="mt-10 grid gap-6 md:grid-cols-2 fade-up delay-1">
            @forelse ($plans as $plan)
                <article class="rounded-2xl border border-zinc-200/70 bg-white/85 p-6 shadow-[0_20px_60px_-45px_rgba(0,0,0,0.45)] backdrop-blur transition duration-300 hover:-translate-y-1 dark:border-zinc-800/80 dark:bg-zinc-900/80">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $plan->name }}</h2>
                        <span class="rounded-full bg-accent/10 px-2.5 py-1 text-xs font-semibold text-accent ring-1 ring-accent/20">
                            {{ t('Monthly') }}
                        </span>
                    </div>

                    <div class="mt-4 flex items-end gap-2">
                        <span class="text-4xl font-black tracking-tight text-accent">R$ {{ $plan->price }}</span>
                        <span class="pb-1 text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ t('per month') }}</span>
                    </div>

                    <p class="mt-4 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">
                        {{ $plan->description }}
                    </p>

                    <div class="mt-6">
                        <flux:button variant="primary" href="{{ route('checkout.index', $plan->id) }}" class="w-full">
                            {{ t('Subscribe to plan') }}
                        </flux:button>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-zinc-200/70 bg-white/80 p-10 text-center text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900/70 dark:text-zinc-300">
                    {{ t('No plan found.') }}
                </div>
            @endforelse
        </section>

        <flux:separator class="mt-16" />

        <section class="mt-16 fade-up delay-2">
            <h2 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white md:text-4xl">
                {{ t('What you will get access to') }}
            </h2>

            <div class="mt-8 grid gap-6 md:grid-cols-2">
                <article class="rounded-2xl border border-zinc-200/70 bg-white/85 p-6 shadow-[0_20px_60px_-45px_rgba(0,0,0,0.45)] backdrop-blur dark:border-zinc-800/80 dark:bg-zinc-900/80">
                    <ul class="space-y-5">
                        <li class="flex items-start gap-4">
                            <div class="rounded-xl bg-emerald-500/15 p-2">
                                <flux:icon.bolt variant="solid" class="text-emerald-500" />
                            </div>
                            <div>
                                <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ t('100,000+ editable files') }}</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ t('PSDs, elements, images, and mockups.') }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="rounded-xl bg-emerald-500/15 p-2">
                                <flux:icon.bolt variant="solid" class="text-emerald-500" />
                            </div>
                            <div>
                                <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ t('Daily downloads') }}</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ t('Fast access to the most used files.') }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="rounded-xl bg-emerald-500/15 p-2">
                                <flux:icon.bolt variant="solid" class="text-emerald-500" />
                            </div>
                            <div>
                                <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ t('Frequent updates') }}</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ t('New collections and resources every week.') }}</p>
                            </div>
                        </li>
                    </ul>
                </article>

                <article class="overflow-hidden rounded-2xl border border-zinc-200/70 bg-white/85 shadow-[0_20px_60px_-45px_rgba(0,0,0,0.45)] backdrop-blur dark:border-zinc-800/80 dark:bg-zinc-900/80">
                    <video class="h-full w-full object-cover" autoplay loop playsinline preload="auto" muted>
                        <source src="https://app.baixardesign.com.br/arquivos-editaveis.mp4" type="video/mp4">
                        {{ t('Your browser does not support HTML5 videos.') }}
                    </video>
                </article>
            </div>
        </section>

        <flux:separator class="mt-16" />

        <section class="mt-16 pb-10 text-center fade-up delay-3">
            <h3 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white md:text-4xl">
                {{ t('Still have questions?') }}
            </h3>
            <p class="mx-auto mt-3 max-w-2xl text-sm text-zinc-500 dark:text-zinc-300">
                {{ t('Talk to our team to clarify details about the ideal plan for your needs.') }}
            </p>
            <div class="mt-7 flex flex-wrap items-center justify-center gap-4">
                <flux:button variant="primary" color="green" icon="phone">WhatsApp</flux:button>
                <flux:button variant="primary" color="blue" icon="mail">{{ t('Email') }}</flux:button>
            </div>
        </section>
    </div>
</div>
