<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .fade-up { animation: fadeUp .6s ease-out both; }
    .fade-in { animation: fadeIn .6s ease-out both; }
    .delay-1 { animation-delay: .08s; }
    .delay-2 { animation-delay: .16s; }
    .delay-3 { animation-delay: .24s; }
    .delay-4 { animation-delay: .32s; }
</style>

<div class="container mx-auto mt-10">
    @php
        $hasRecommended = collect($recomendadas ?? [])->count() > 0;
        $hasFollowed = collect($dosSeguidos ?? [])->count() > 0;
        $hasCategoryCollections = collect($categorias ?? [])->contains(
            fn($c) => isset($c->collections) && $c->collections->isNotEmpty(),
        );
        $hasAnyCollections = $hasRecommended || $hasFollowed || $hasCategoryCollections;
        $hasAnyItems = collect($bestSellers ?? [])->count() > 0;
    @endphp

    {{-- Header / Hero Banner --}}
    <div class="mb-8 fade-up">
        <div
            class="relative overflow-hidden rounded-2xl border border-zinc-200/70 dark:border-zinc-800/80 min-h-[220px] md:min-h-[260px]">
            <div class="absolute inset-0 bg-gradient-to-r from-zinc-900 via-zinc-800 to-zinc-900"></div>
            <div class="absolute -left-20 -top-16 h-48 w-48 rounded-full bg-accent/35 blur-3xl"></div>
            <div class="absolute right-[-4rem] top-8 h-56 w-56 rounded-full bg-blue-400/25 blur-3xl"></div>
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/30 to-transparent"></div>
            <div class="absolute inset-0 opacity-20"
                style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.35) 1px, transparent 0); background-size: 22px 22px;">
            </div>

            <div class="relative flex h-full min-h-[220px] md:min-h-[260px] flex-col justify-end gap-3 p-6 md:p-8">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-white md:text-4xl">
                        {{ t('Create. Publish. Scale.') }}
                    </h1>
                    <p class="mt-1 text-sm md:text-base text-white/80">
                        {{ t('Premium templates and collections to speed up your creative flow.') }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <flux:button variant="primary" href="{{ route('collection.index') }}">{{ t('Explore') }}</flux:button>
                </div>
            </div>
        </div>
    </div>

    @if (!$hasAnyCollections && !$hasAnyItems)
        <div class="mb-8 fade-up">
            <div class="relative overflow-hidden rounded-2xl border border-zinc-200/70 dark:border-zinc-800/80 bg-white/80 dark:bg-zinc-900/70 p-6 md:p-8">
                <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-accent/15 blur-3xl"></div>
                <div class="absolute -left-10 -bottom-16 h-48 w-48 rounded-full bg-blue-400/15 blur-3xl"></div>

                <div class="relative flex flex-col gap-3">
                    <div class="text-xs font-semibold uppercase tracking-widest text-accent">
                        {{ t('Empty home') }}
                    </div>
                    <h2 class="text-2xl font-black text-zinc-900 dark:text-white">
                        {{ t('No collections or items yet') }}
                    </h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-300 max-w-2xl">
                        {{ t('When new collections or template items are published, they will appear here. For now, check back soon or explore all collections.') }}
                    </p>
                    <div class="flex flex-wrap gap-2 pt-2">
                        <flux:button variant="primary" href="{{ route('collection.index') }}">
                            {{ t('Explore collections') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 3 Colunas --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- COLUNA ESQUERDA (Sidebar) --}}
        <aside class="lg:col-span-3 fade-up delay-1">
            <div class="lg:sticky lg:top-36 space-y-4">
                {{-- Card: Busca / Ações --}}
                <div class="rounded-2xl border border-zinc-200/70 dark:border-zinc-700 bg-white/70 dark:bg-zinc-900/80 backdrop-blur p-4 shadow-[0_20px_60px_-50px_rgba(0,0,0,0.35)]">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-zinc-900 dark:text-white">{{ t('Shortcuts') }}</p>
                    </div>

                    <div class="mt-3 grid gap-2">
                        <a href="{{ route('collection.index') }}"
                           class="rounded-xl px-3 py-2 text-sm bg-zinc-50/80 dark:bg-zinc-950 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition">
                            {{ t('View all collections') }}
                        </a>
                        <a href="{{ route('collection.index') }}?sort=trending"
                           class="rounded-xl px-3 py-2 text-sm bg-zinc-50/80 dark:bg-zinc-950 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition">
                            {{ t('Trending') }}
                        </a>
                        <a href="{{ route('collection.index') }}?sort=recent"
                           class="rounded-xl px-3 py-2 text-sm bg-zinc-50/80 dark:bg-zinc-950 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition">
                            {{ t('Recent') }}
                        </a>
                    </div>
                </div>

                {{-- Card: Categorias populares (lista compacta) --}}
                <div class="rounded-2xl border border-zinc-200/70 dark:border-zinc-700 bg-white/70 dark:bg-zinc-900/80 backdrop-blur p-4 shadow-[0_20px_60px_-50px_rgba(0,0,0,0.35)]">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-zinc-900 dark:text-white">{{ t('Categories') }}</p>
                        <a href="{{ route('collection.index') }}"
                           class="text-sm text-accent hover:underline">{{ t('View all') }}</a>
                    </div>

                    <div class="mt-3 space-y-2">
                        @foreach ($categories->take(8) as $cat)
                            <a href="{{ route('collection.category', ['category' => $cat->slug]) }}"
                               class="group flex items-center justify-between rounded-xl px-3 py-2 bg-zinc-50/80 dark:bg-zinc-900/60 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition">
                                <span class="text-sm font-medium text-zinc-800 dark:text-zinc-100 truncate">
                                    {{ $cat->name }}
                                </span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $cat->items_count }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        {{-- COLUNA CENTRAL (Feed) --}}
        <main class="lg:col-span-6 space-y-8 fade-up delay-2">
            {{-- Destaque: Categorias populares (cards horizontais) --}}
            <section class="space-y-3 fade-up delay-2">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl md:text-lg font-bold text-zinc-900 dark:text-white">{{ t('Popular Categories') }}</h2>
                    <flux:button variant="primary" href="{{ route('collection.index') }}">{{ t('View all') }}</flux:button>
                </div>

                <div class="flex gap-4 overflow-x-auto scrollbar-hide pb-2">
                    @foreach ($categories as $cat)
                        @php
                            $thumb = $cat->image_url;
                        @endphp

                        <a href="{{ route('collection.category', ['category' => $cat->slug]) }}"
                           class="group relative min-w-[280px] max-w-[280px] h-[160px] rounded-2xl overflow-hidden border border-zinc-200/40 dark:border-zinc-800/60 bg-zinc-100 dark:bg-zinc-900 transition hover:-translate-y-1 hover:shadow-lg">
                            <img
                                src="{{ $thumb }}"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                alt=""
                            />
                            <div class="absolute inset-0 bg-gradient-to-tr from-black/70 via-black/30 to-transparent"></div>

                            <div class="absolute inset-0 p-4 flex flex-col justify-end">
                                <div class="flex items-end justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-white font-semibold truncate">{{ $cat->name }}</p>
                                        <p class="text-white/80 text-xs">{{ $cat->items_count }} {{ t('items') }}</p>
                                    </div>
                                    <span class="text-white/90 text-xs px-2 py-1 rounded-full bg-white/10 border border-white/10">
                                        {{ t('Explore') }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- Recomendadas --}}
            <section class="space-y-3 fade-up delay-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl md:text-lg font-bold text-zinc-900 dark:text-white">{{ t('Recommended Collections') }}</h2>
                    <flux:button variant="primary" href="{{ route('collection.index') }}">{{ t('View all') }}</flux:button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($recomendadas as $index => $colecao)
                        <x-card-collection :colecao="$colecao" :index="$index" />
                    @endforeach
                </div>
            </section>

            @auth
                {{-- Dos Seguidos --}}
                <section class="space-y-3 fade-up delay-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl md:text-lg font-bold text-zinc-900 dark:text-white">{{ t('From who you follow') }}</h2>
                        <flux:button href="{{ route('collection.index') }}" variant="primary">{{ t('View all') }}</flux:button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($dosSeguidos as $index => $colecao)
                            <x-card-collection :colecao="$colecao" :index="$index" />
                        @empty
                            <div class="rounded-2xl w-full border border-zinc-200/70 dark:border-zinc-800 bg-white/70 dark:bg-zinc-900/70 p-5 text-sm text-zinc-500 dark:text-zinc-100">
                                {{ t('There are no new collections from creators you follow yet.') }}
                            </div>
                        @endforelse
                    </div>
                </section>
            @endauth

            {{-- Feed por Categorias --}}
            @foreach ($categorias as $categoria)
                <section class="space-y-3 fade-up">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl md:text-lg font-bold text-zinc-900 dark:text-white">{{ $categoria->name }}</h2>
                        <flux:button href="{{ route('collection.index') }}" variant="primary">{{ t('View all') }}</flux:button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($categoria->collections->take(6) as $index => $colecao)
                            <x-card-collection :colecao="$colecao" :index="$index" />
                        @endforeach
                    </div>
                </section>
            @endforeach
        </main>

        {{-- COLUNA DIREITA (Sidebar) --}}
        <aside class="lg:col-span-3 fade-up delay-3">
            <div class="lg:sticky lg:top-36 space-y-4">
                {{-- Autores em destaque --}}
                <div class="rounded-2xl border border-zinc-200/70 dark:border-zinc-700 bg-white/70 dark:bg-zinc-900/80 backdrop-blur p-4 shadow-[0_20px_60px_-50px_rgba(0,0,0,0.35)]">
                    <style>
                        @keyframes profileRingSpinSm {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                        .profile-ring-sm {
                            position: relative;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            isolation: isolate;
                        }
                        .profile-ring-sm::before {
                            content: "";
                            position: absolute;
                            inset: -4px;
                            border-radius: 9999px;
                            background: var(--ring-gradient);
                            animation: profileRingSpinSm var(--ring-speed) linear infinite;
                            filter: blur(1px);
                            opacity: 0.9;
                            z-index: 0;
                            pointer-events: none;
                        }
                        .profile-ring-sm::after {
                            content: "";
                            position: absolute;
                            inset: -1px;
                            border-radius: 9999px;
                            border: 2px solid var(--ring-border);
                            opacity: 0.7;
                            z-index: 0;
                            pointer-events: none;
                        }
                    </style>
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-zinc-900 dark:text-white">{{ t('Featured Creators') }}</p>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-3">
                        @foreach ($featuredArtists as $user)
                            <a href="/profile/{{ $user->slug }}"
                               class="group text-center">
                                @php
                                    $avatar = $user->avatar();
                                    $animationsAllowed =
                                        $user->type === 'verified' &&
                                        ($user->profile_animations_enabled ?? true);
                                    $ringStyle = $user->profileRingStyle;
                                    $avatarUrl = $avatar['type'] === 'image' ? $avatar['value'] : null;
                                    if (
                                        !$animationsAllowed &&
                                        $user->profile_image &&
                                        str_ends_with(strtolower($user->profile_image), '.gif')
                                    ) {
                                        $staticPath = preg_replace('/\\.gif$/i', '.png', $user->profile_image);
                                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($staticPath)) {
                                            $avatarUrl = asset('storage/' . $staticPath);
                                        } else {
                                            $avatarUrl = null;
                                        }
                                    }
                                @endphp

                                @if ($avatarUrl)
                                    <div
                                        class="mx-auto {{ $animationsAllowed ? 'profile-ring-sm' : '' }}"
                                        @if ($animationsAllowed)
                                            style="
                                                --ring-gradient: {{ $ringStyle?->gradient ?? 'conic-gradient(from 120deg, #22d3ee, #6366f1, #f97316, #22d3ee)' }};
                                                --ring-border: {{ $ringStyle?->border ?? 'rgba(255,255,255,0.25)' }};
                                                --ring-speed: {{ $ringStyle?->speed ?? '8s' }};
                                            "
                                        @endif
                                    >
                                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}"
                                             class="relative z-10 h-14 w-14 rounded-full object-cover ring-2 ring-transparent group-hover:ring-accent/60 transition">
                                    </div>
                                @else
                                    <div class="mx-auto h-14 w-14 rounded-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center font-bold text-zinc-700 dark:text-zinc-200">
                                        {{ $avatar['value'] }}
                                    </div>
                                @endif

                                <p class="mt-2 text-xs font-semibold text-zinc-700 dark:text-zinc-100 truncate">
                                    {{ $user->name }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Mais vendidos --}}
                <div class="rounded-2xl border border-zinc-200/70 dark:border-zinc-700 bg-white/70 dark:bg-zinc-900/80 backdrop-blur p-4 shadow-[0_20px_60px_-50px_rgba(0,0,0,0.35)]">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-zinc-900 dark:text-white">{{ t('Best sellers') }}</p>
                    </div>

                    @if ($bestSellers && $bestSellers->count())
                        <div class="mt-3 space-y-2">
                            @foreach ($bestSellers as $item)
                                <a
                                    href="{{ $item->collection ? route('collection.show', $item->collection) : '#' }}"
                                    class="flex items-center gap-3 rounded-xl border border-zinc-200/60 bg-white/70 px-3 py-2 text-sm transition hover:bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-900/60 dark:hover:bg-zinc-800"
                                >
                                    <img
                                        src="{{ $item->preview_url }}"
                                        class="h-10 w-14 rounded-md border border-zinc-200/60 object-cover dark:border-zinc-800"
                                        alt="{{ $item->name }}"
                                    />
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate font-semibold text-zinc-800 dark:text-zinc-100">
                                            {{ $item->name }}
                                        </div>
                                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                            {{ (int) ($item->sold_qty ?? 0) }} {{ t('sold') }}
                                        </div>
                                    </div>
                                    <div class="text-right text-xs font-semibold text-accent">
                                        R$ {{ number_format((float) $item->price, 2, ',', '.') }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-3 rounded-xl border border-zinc-200/70 bg-white/70 p-3 text-xs text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900/60 dark:text-zinc-300">
                            {{ t('No sales yet.') }}
                        </div>
                    @endif
                </div>

                {{-- Card: Tips / Trending --}}
                <div class="rounded-2xl border border-zinc-200/70 dark:border-zinc-700 bg-white/70 dark:bg-zinc-900/80 backdrop-blur p-4 shadow-[0_20px_60px_-50px_rgba(0,0,0,0.35)]">
                    <p class="font-semibold text-zinc-900 dark:text-white">{{ t('Trending') }}</p>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ t('See collections growing in followers and views.') }}
                    </p>

                    <div class="mt-3">
                        <a href="{{ route('collection.index') }}?sort=trending"
                           class="inline-flex items-center justify-center w-full rounded-md px-3 py-2 text-sm
                                  bg-accent text-white hover:opacity-90 transition">
                            {{ t('View trending') }}
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
