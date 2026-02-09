<div class="min-h-screen px-4 py-10 md:px-8">
    <div class="mx-auto max-w-6xl space-y-8">
        <div class="relative overflow-hidden rounded-3xl border border-zinc-200/70 bg-white/80 p-6 shadow-[0_30px_70px_-50px_rgba(0,0,0,0.45)] dark:border-zinc-800 dark:bg-zinc-900/70 md:p-8">
            <div class="absolute -right-24 -top-24 h-48 w-48 rounded-full bg-accent/20 blur-3xl"></div>
            <div class="absolute -left-16 bottom-0 h-40 w-40 rounded-full bg-blue-500/15 blur-3xl"></div>
            <div class="relative flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">{{ t('Your collection') }}</p>
                    <h1 class="mt-2 text-3xl md:text-4xl font-black tracking-tight text-zinc-900 dark:text-white">{{ t('Wishlist') }}</h1>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ t('Saved items to buy or download later.') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="rounded-2xl border border-zinc-200/60 bg-white/70 px-4 py-3 text-center text-xs font-semibold text-zinc-600 dark:border-zinc-800 dark:bg-zinc-900/70 dark:text-zinc-300">
                        {{ t('Total saved') }}
                        <div class="mt-1 text-2xl font-black text-zinc-900 dark:text-white">{{ $items->count() }}</div>
                    </div>
                    <a href="{{ route('collection.index') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-accent px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                        {{ t('Explore collections') }}
                    </a>
                </div>
            </div>
        </div>

        @if ($items->isEmpty())
            <div class="rounded-3xl border border-dashed border-zinc-200/70 bg-white/70 p-12 text-center text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900/60 dark:text-zinc-300">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-accent">
                    <flux:icon name="heart" class="size-5" />
                </div>
                <p class="text-base font-semibold">{{ t('Your wishlist is empty.') }}</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ t('Save items to review later.') }}</p>
                <a href="{{ route('collection.index') }}"
                   class="mt-4 inline-flex items-center justify-center rounded-xl bg-accent px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90">
                    {{ t('View collections') }}
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $item)
                    <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/70 bg-white/80 shadow-[0_24px_60px_-48px_rgba(0,0,0,0.45)] transition hover:-translate-y-1 hover:shadow-[0_28px_70px_-45px_rgba(0,0,0,0.55)] dark:border-zinc-800 dark:bg-zinc-900/70">
                        <div class="relative">
                            <img
                                src="{{ $item->preview_url }}"
                                class="h-[220px] w-full object-cover"
                                alt="{{ $item->name }}"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent opacity-80"></div>
                            <div class="absolute left-4 top-4 inline-flex items-center rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold uppercase text-zinc-700 ring-1 ring-white/60">
                                {{ $item->type }}
                            </div>
                            @if ($item->type === 'sites')
                                <div class="absolute right-4 top-4 rounded-full bg-accent px-2.5 py-1 text-[11px] font-semibold text-white shadow">
                                    R$ {{ number_format((float) $item->price, 2, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="text-base font-extrabold tracking-tight text-zinc-900 dark:text-white line-clamp-1">
                                    {{ $item->name }}
                                </h3>
                                <flux:button size="xs" variant="outline" wire:click="remove({{ $item->id }})">{{ t('Remove') }}</flux:button>
                            </div>

                            <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                                @if ($item->type === 'sites')
                                    <span>{{ t('Single purchase') }}</span>
                                @else
                                    <span>{{ t('Available for download') }}</span>
                                @endif
                                <a href="{{ $item->collection ? route('collection.show', $item->collection) : '#' }}"
                                   class="font-semibold text-accent hover:underline">
                                    {{ t('View item') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
