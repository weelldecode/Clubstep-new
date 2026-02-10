{{-- resources/views/livewire/app/collections/show.blade.php --}}
@php
    $type = strtolower($collection->type ?? 'mockups');
    $images = $previewImages ?? [];
    $previewCount = count($images);
    $hero = $images[0] ?? null;
    $itemPlaceholder = asset('images/placeholders/item-default.svg');
    $filesCount = $collection->items_count ?? $collection->items->count();
    $author = $collection->user;
    $authorAnimationsAllowed = $author
        ? ($author->type === 'verified' && ($author->profile_animations_enabled ?? true))
        : false;
    $authorRingStyle = $author?->profileRingStyle;
    $authorAvatarUrl = null;
    if ($author && $author->profile_image) {
        $authorAvatarUrl = asset('storage/' . $author->profile_image);
        if (
            !$authorAnimationsAllowed &&
            str_ends_with(strtolower($author->profile_image), '.gif')
        ) {
            $staticPath = preg_replace('/\\.gif$/i', '.png', $author->profile_image);
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($staticPath)) {
                $authorAvatarUrl = asset('storage/' . $staticPath);
            } else {
                $authorAvatarUrl = null;
            }
        }
    }
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "CreativeWork",
        "name" => $collection->name,
        "description" => $collection->description,
        "image" => $collection->cover_url,
        "url" => url()->current(),
        "author" => $author
            ? [
                "@type" => "Person",
                "name" => $author->name,
                "url" => route("profile.user", ["user" => $author->slug]),
            ]
            : null,
    ];
@endphp

@push('seo')
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

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

<div class="relative overflow-hidden">
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes glowPulse {
            0%, 100% { opacity: .35; transform: translateY(0); }
            50% { opacity: .6; transform: translateY(-6px); }
        }
        .fade-up { animation: fadeUp .7s ease-out both; }
        .delay-1 { animation-delay: .08s; }
        .delay-2 { animation-delay: .16s; }
        .delay-3 { animation-delay: .24s; }
        .glow-pulse { animation: glowPulse 6s ease-in-out infinite; }
    </style>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="absolute inset-x-0 top-0 h-1 bg-accent/80"></div>
    </div>

    <div class="relative px-4 md:px-8">
        {{-- HERO --}}
        <section class="container mx-auto mt-6 mb-8 fade-up">
            <livewire:components.breadcrumb />

            <div class="mt-4 grid grid-cols-1 lg:grid-cols-[1.2fr_0.8fr] gap-6 items-end">
                <div class="min-w-0">
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight text-zinc-900 dark:text-white truncate">
                        {{ $collection->name }}
                    </h1>
                    <p class="mt-2 text-sm md:text-base text-zinc-500 dark:text-zinc-400 max-w-3xl">
                        {{ $collection->description }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-xs justify-start lg:justify-end">
                    <span class="inline-flex items-center rounded-full px-3 py-1 font-semibold
                                 bg-white/70 dark:bg-zinc-900/80 text-zinc-700 dark:text-zinc-200
                                 ring-1 ring-zinc-200/60 dark:ring-zinc-800 backdrop-blur">
                        {{ $filesCount }} {{ Str::plural('arquivo', $filesCount) }}
                    </span>
                    @if(!empty($collection->type))
                        <span class="inline-flex items-center rounded-full px-3 py-1 font-semibold uppercase
                                     bg-accent/10 text-accent ring-1 ring-accent/20">
                            {{ $collection->type }}
                        </span>
                    @endif
                    @if ($author)
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 font-semibold
                                     bg-white/70 dark:bg-zinc-900/80 text-zinc-700 dark:text-zinc-200
                                     ring-1 ring-zinc-200/60 dark:ring-zinc-800 backdrop-blur">
                            <span class="h-1.5 w-1.5 rounded-full bg-accent/80"></span>
                            {{ $author->name }}
                        </span>
                    @endif
                </div>
            </div>

            <flux:separator class="mt-6" variant="subtle" />
        </section>

        {{-- BODY --}}
        <div class="container mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- LEFT: PREVIEW + META --}}
                <aside class="lg:col-span-4 fade-up delay-1">
                    <div class="lg:sticky lg:top-24 space-y-5">
                        <div class="rounded-2xl overflow-hidden border border-zinc-200/70 dark:border-zinc-700 bg-white/70 dark:bg-zinc-900/80 backdrop-blur shadow-[0_20px_60px_-50px_rgba(0,0,0,0.35)]">
                            <div class="p-4 flex items-center justify-between">
                                <div>
                                <h3 class="text-sm font-bold tracking-wide text-zinc-700 dark:text-zinc-100">{{ t('Preview') }}</h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $type === 'sites' ? t('Website screenshot') : t('Collection images') }}
                                </p>
                                </div>

                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase
                                             bg-white/30 dark:bg-white/10 text-zinc-700 dark:text-zinc-100 ring-1 ring-zinc-200/60 dark:ring-white/10">
                                    {{ $type }}
                                </span>
                            </div>

                            <div class="px-4 pb-4">
                                @if($type === 'sites')
                                    <div class="rounded-2xl overflow-hidden border border-zinc-200/60 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-900">
                                        <div class="flex items-center gap-2 px-4 py-3 bg-white/70 dark:bg-zinc-700/100 border-b border-zinc-200/60 dark:border-zinc-800">
                                            <span class="h-2.5 w-2.5 rounded-full bg-red-400/80"></span>
                                            <span class="h-2.5 w-2.5 rounded-full bg-yellow-400/80"></span>
                                            <span class="h-2.5 w-2.5 rounded-full bg-green-400/80"></span>
                                            <div class="ml-3 h-7 flex-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-800"></div>
                                        </div>

                                        @if($hero)
                                            <img
                                                src="{{ asset('storage/' . $hero) }}"
                                                class="aspect-[4/3] w-full object-cover"
                                                alt=""
                                                loading="lazy"
                                            />
                                        @else
                                            <img
                                                src="{{ $collection->cover_url }}"
                                                class="aspect-[4/3] w-full object-cover"
                                                alt=""
                                                loading="lazy"
                                            />
                                        @endif
                                    </div>
                                @else
                                    @if ($previewCount > 0)
                                        <div class="grid gap-1 {{ $previewCount === 1 ? 'grid-cols-1' : 'grid-cols-2' }}">
                                            @foreach ($images as $img)
                                                <img
                                                    src="{{ asset('storage/' . $img) }}"
                                                    class="w-full h-[190px] object-cover rounded-xl border border-zinc-200/60 dark:border-zinc-800"
                                                    alt=""
                                                    loading="lazy"
                                                >
                                            @endforeach

                                            @for ($i = $previewCount; $i < 4; $i++)
                                                @if($previewCount > 1)
                                                    <img
                                                        src="{{ $itemPlaceholder }}"
                                                        class="w-full h-[190px] object-cover rounded-xl border border-zinc-200/60 dark:border-zinc-800"
                                                        alt=""
                                                        loading="lazy"
                                                    >
                                                @endif
                                            @endfor
                                        </div>
                                    @else
                                        <div class="grid grid-cols-2 gap-1">
                                            @for ($i = 0; $i < 4; $i++)
                                                <img
                                                    src="{{ $itemPlaceholder }}"
                                                    class="w-full h-[190px] object-cover rounded-xl border border-zinc-200/60 dark:border-zinc-800"
                                                    alt=""
                                                    loading="lazy"
                                                >
                                            @endfor
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="rounded-2xl border border-zinc-200/70 dark:border-zinc-700 bg-white/70 dark:bg-zinc-900/80 backdrop-blur p-5 shadow-[0_20px_60px_-50px_rgba(0,0,0,0.35)]">
                            @if ($author)
                                <div class="flex items-center gap-4">
                                    @if ($authorAvatarUrl)
                                        <div
                                            class="{{ $authorAnimationsAllowed ? 'profile-ring-sm' : '' }}"
                                            @if ($authorAnimationsAllowed)
                                                style="
                                                    --ring-gradient: {{ $authorRingStyle?->gradient ?? 'conic-gradient(from 120deg, #22d3ee, #6366f1, #f97316, #22d3ee)' }};
                                                    --ring-border: {{ $authorRingStyle?->border ?? 'rgba(255,255,255,0.25)' }};
                                                    --ring-speed: {{ $authorRingStyle?->speed ?? '8s' }};
                                                "
                                            @endif
                                        >
                                            <img src="{{ $authorAvatarUrl }}"
                                                 class="relative z-10 w-11 h-11 rounded-full object-cover"
                                                 alt="{{ $author->name }}">
                                        </div>
                                    @else
                                        <div class="w-11 h-11 rounded-full bg-zinc-300 flex items-center justify-center text-sm font-bold text-white">
                                            {{ $author->initials() }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-extrabold text-zinc-900 dark:text-white truncate">{{ $author->name }}</p>
                                            @if ($author->type === 'verified')
                                                <flux:icon.badge-check class="size-4 text-accent" />
                                            @endif
                                        </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Collection author') }}</p>
                                    </div>
                                </div>

                                <flux:separator class="my-4" />
                            @endif

                            <div class="flex flex-wrap gap-2">
                                @php
                                    $collectionTags = $collection->tags
                                        ->merge($collection->legacyTags)
                                        ->unique("id")
                                        ->values();
                                @endphp
                                @forelse ($collectionTags as $tag)
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold
                                                 bg-accent/10 text-accent ring-1 ring-accent/20">
                                        {{ $tag->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('No tags.') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </aside>

                {{-- RIGHT: ITEMS --}}
                <main class="lg:col-span-8 space-y-4 fade-up delay-2">
                    <div class="rounded-2xl border border-zinc-200/70 dark:border-zinc-700 bg-white/70 dark:bg-zinc-900/80 backdrop-blur p-4 shadow-[0_20px_60px_-50px_rgba(0,0,0,0.35)]">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="w-full sm:w-[260px]">
                                <x-select
                                    wire:model.live="sortField"
                                    placeholder="{{ t('Sort by') }}"
                                    :options="[
                                        ['name' => t('Name'), 'id' => 'name'],
                                        ['name' => t('Created date'), 'id' => 'created_at'],
                                    ]"
                                    option-label="name"
                                    option-value="id"
                                />
                            </div>

                            <flux:button wire:click="toggleSortDirection" variant="outline" title="Alternar ordem">
                                @if ($sortDirection === 'asc')
                                    ↑ {{ t('Ascending') }}
                                @else
                                    ↓ {{ t('Descending') }}
                                @endif
                            </flux:button>
                        </div>
                    </div>

                    <div wire:loading class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @for($i=0;$i<6;$i++)
                            <div class="h-[300px] rounded-2xl bg-zinc-100 dark:bg-zinc-900 animate-pulse"></div>
                        @endfor
                    </div>

                    <div wire:loading.remove class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @forelse($items as $index => $item)
                            <div
                                x-data="{ visible: false }"
                                x-init="setTimeout(() => visible = true, {{ $index * 70 }})"
                                :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-3'"
                                wire:click="showItem({{ $item->id }})"
                                class="opacity-0 translate-y-3 transition-all duration-500 ease-out cursor-pointer"
                                role="button"
                                tabindex="0"
                            >
                                <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/60 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-900 shadow-[0_20px_50px_-40px_rgba(0,0,0,0.35)]">
                                    <img
                                        src="{{ $item->preview_url }}"
                                        class="h-[300px] w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                        alt="{{ $item->name }}"
                                        loading="lazy"
                                    />

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-90"></div>

                                    <div class="absolute inset-x-0 bottom-0 p-4">
                                        <h3 class="text-sm font-extrabold tracking-tight text-white line-clamp-1">
                                            {{ $item->name }}
                                        </h3>
                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px]">
                                            <span class="inline-flex items-center rounded-full bg-white/15 px-2 py-0.5 font-semibold uppercase text-white/90 ring-1 ring-white/20">
                                                {{ $item->type }}
                                            </span>
                                            @if ($item->type === 'sites')
                                                <span class="inline-flex items-center rounded-full bg-accent/80 px-2 py-0.5 font-semibold text-white">
                                                    R$ {{ number_format((float) $item->price, 2, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>

                                        <p class="mt-2 text-[11px] text-white/70">
                                            {{ t('Click to view details') }}
                                        </p>

                                        @if ($item->type === 'sites')
                                            @php
                                                $alreadyPurchased = $this->hasPurchasedItem($item->id);
                                                $cartHasItem = $this->hasActiveCartItem();
                                                $itemInCart = $this->isItemInCart($item->id);
                                            @endphp
                                            <div class="mt-3">
                                                @if ($alreadyPurchased)
                                                    <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-3 py-1 text-[11px] font-semibold text-emerald-100">
                                                        Já comprado
                                                    </span>
                                                @elseif ($cartHasItem && !$itemInCart)
                                                    <a
                                                        href="{{ route('cart.index') }}"
                                                        class="inline-flex items-center rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-zinc-900 transition hover:bg-white"
                                                    >
                                                        Finalizar compra
                                                    </a>
                                                @elseif ($itemInCart)
                                                    <a
                                                        href="{{ route('cart.index') }}"
                                                        class="inline-flex items-center rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-zinc-900 transition hover:bg-white"
                                                    >
                                                        No carrinho
                                                    </a>
                                                @else
                                                    <button
                                                        type="button"
                                                        wire:click.stop="addToCart({{ $item->id }})"
                                                        class="inline-flex items-center rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-zinc-900 transition hover:bg-white"
                                                    >
                                                        {{ t('Buy now') }}
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full flex flex-col items-center justify-center py-16 text-zinc-500 dark:text-zinc-200">
                                <flux:icon name="layers-2" class="w-10 h-10 mb-3 text-accent"/>
                                <p class="text-lg font-semibold">{{ t('No items found') }}</p>
                                <p class="text-sm">{{ t('Try changing the sorting or add new items.') }}</p>
                            </div>
                        @endforelse
                    </div>

                    <section class="pt-6 fade-up delay-3">
                        <flux:separator />

                        <div class="mt-6">
                            <div class="flex items-end justify-between">
                                <h2 class="text-xl font-extrabold tracking-tight text-zinc-900 dark:text-white">
                                    {{ t('Related collections') }}
                                </h2>
                            </div>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                                @forelse($relatedCollections as $rel)
                                    <a href="{{ route('collection.show', $rel) }}"
                                       class="rounded-2xl border border-zinc-200/70 dark:border-zinc-800
                                              bg-white/70 dark:bg-zinc-950/40 backdrop-blur p-4
                                              hover:-translate-y-1 hover:shadow-[0_20px_50px_-30px_rgba(0,0,0,0.45)] transition">
                                        <h3 class="font-extrabold text-zinc-900 dark:text-white line-clamp-1">{{ $rel->name }}</h3>
                                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                            {{ Str::limit($rel->description, 80) }}
                                        </p>
                                    </a>
                                @empty
                                    <div class="col-span-full text-center text-zinc-500 dark:text-zinc-300 py-10">
                                        {{ t('No related collections.') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>

        {{-- MODAL --}}
        @include('livewire.app.collections.group.item-modal')
    </div>
</div>
 
