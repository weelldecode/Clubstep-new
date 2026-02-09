<ul class="space-y-4">
    @forelse($collections as $index => $collection)
        <li
            x-data="{ visible: false }"
            x-init="setTimeout(() => visible = true, {{ $index * 70 }})"
            :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-3'"
            class="opacity-0 translate-y-3 transition-all duration-500 ease-out"
        >
            <a href="/collection/v/{{ $collection->slug }}"
               class="group block rounded-lg border border-zinc-200/70 dark:border-zinc-700
                      bg-white/60 dark:bg-zinc-900 backdrop-blur
                      hover:-translate-y-1 hover:shadow-lg hover:shadow-black/5 dark:hover:shadow-black/30
                      transition-all duration-300">

                <div class="flex flex-col md:flex-row gap-4 p-4">

                    @php
                        $allImages = [];
                        foreach ($collection->items as $item) {
                            $itemImages = is_array($item->images) ? $item->images : json_decode($item->images, true);
                            if ($itemImages) $allImages = array_merge($allImages, $itemImages);
                        }
                        $hero = $allImages[0] ?? null;

                        $count = $collection->items_count ?? $collection->items->count();
                    @endphp

                    {{-- Thumbnail --}}
                    <div class="relative w-full md:w-[220px] flex-shrink-0 overflow-hidden rounded-lg border border-zinc-200/60 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-700">
                        @if ($hero)
                            <img
                                src="{{ asset('storage/' . $hero) }}"
                                alt="{{ $collection->name }}"
                                class="aspect-[16/10] w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                loading="lazy"
                            />
                        @else
                            <img
                                src="{{ $collection->cover_url }}"
                                alt="{{ $collection->name }}"
                                class="aspect-[16/10] w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                loading="lazy"
                            />
                        @endif

                        {{-- overlay + badge count --}}
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/45 via-black/0 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                        <div class="absolute left-3 top-3">
                            <span class="inline-flex items-center rounded-full bg-black/50 px-2.5 py-1 text-[11px] font-semibold text-white ring-1 ring-white/10">
                                {{ $count }} {{ Str::plural(t('file'), $count) }}
                            </span>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="min-w-0 flex-1 flex flex-col justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-lg font-extrabold tracking-tight text-zinc-900 dark:text-white truncate">
                                    {{ $collection->name }}
                                </h3>

                                <span class="hidden md:inline-flex items-center gap-2 text-sm text-accent font-bold">
                                    {{ t('Open') }}
                                    <span class="transition-transform duration-300 group-hover:translate-x-0.5">→</span>
                                </span>
                            </div>

                            @if(!empty($collection->description))
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-300 line-clamp-2">
                                    {{ $collection->description }}
                                </p>
                            @endif
                        </div>

                        {{-- Meta --}}
                        <div class="flex flex-wrap items-center gap-2">
                            {{-- categorias --}}
                            @if ($collection->categories->isNotEmpty())
                                @foreach ($collection->categories->take(4) as $cat)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold
                                                 bg-zinc-100 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-200
                                                 ring-1 ring-zinc-200/60 dark:ring-zinc-800">
                                        {{ $cat->name }}
                                    </span>
                                @endforeach

                                @if($collection->categories->count() > 4)
                                    <span class="text-[11px] text-zinc-400">
                                        +{{ $collection->categories->count() - 4 }}
                                    </span>
                                @endif
                            @endif

                            {{-- CTA no mobile --}}
                            <span class="md:hidden inline-flex items-center gap-2 text-sm text-accent font-bold">
                                {{ t('Open') }}
                                <span class="transition-transform duration-300 group-hover:translate-x-0.5">→</span>
                            </span>
                        </div>
                    </div>

                </div>
            </a>
        </li>
    @empty
        <div class="flex flex-col items-center justify-center py-20 text-zinc-500 dark:text-zinc-100">
            <flux:icon name="layers-2" class="w-10 h-10 mb-3 text-accent dark:text-zinc-100"/>
            <p class="text-lg text-zinc-600 dark:text-zinc-50 font-semibold">{{ t('No collection found.') }}</p>
            <p class="text-sm text-zinc-500 dark:text-zinc-200">{{ t('The selected filter has no collections.') }}</p>
        </div>
    @endforelse
</ul>
