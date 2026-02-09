<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

    @forelse($collections as $index => $collection)

        @php
            $images = [];
            $itemPlaceholder = asset('images/placeholders/item-default.svg');

            foreach ($collection->items as $item) {
                $imgs = is_array($item->images) ? $item->images : json_decode($item->images, true);
                if ($imgs) {
                    foreach ($imgs as $img) {
                        $images[] = $img;
                        if (count($images) === 4) break 2;
                    }
                }
            }

            $imgCount = count($images);
            $filesCount = $collection->items_count ?? $collection->items->count();
            $type = strtolower($collection->type ?? 'mockups');

            $isSite = $type === 'sites';
            $hero = $images[0] ?? null;
        @endphp

        <div
            x-data="{ visible: false }"
            x-init="setTimeout(() => visible = true, {{ $index * 70 }})"
            :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-3'"
            class="opacity-0 translate-y-3 transition-all duration-500 ease-out"
        >
            <a href="/collection/v/{{ $collection->slug }}"
               class="group block rounded-2xl overflow-hidden border border-zinc-200/70 dark:border-zinc-800/80
                      bg-white dark:bg-zinc-900 transition-all duration-300
                      hover:-translate-y-1 hover:shadow-[0_20px_50px_-30px_rgba(0,0,0,0.45)]">

                {{-- HERO / MOSAICO --}}
                <div class="relative">

                    {{-- TYPE = SITES (hero esticado) --}}
                    @if ($isSite)
                        <div class="relative overflow-hidden">
                            @if ($hero)
                                <img
                                    src="{{ asset('storage/' . $hero) }}"
                                    class="aspect-[4/3] w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    alt=""
                                    loading="lazy"
                                />
                            @else
                                <img
                                    src="{{ $collection->cover_url }}"
                                    class="aspect-[4/3] w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    alt=""
                                    loading="lazy"
                                />
                            @endif
                        </div>

                    {{-- OUTROS (mosaico 2x2 premium) --}}
                    @else
                        @if ($imgCount > 0)
                            <div class="grid gap-1 {{ $imgCount === 1 ? 'grid-cols-1' : 'grid-cols-2' }}">
                                @foreach ($images as $img)
                                    <div class="relative overflow-hidden">
                                        <img
                                            src="{{ asset('storage/' . $img) }}"
                                            class="h-[200px] w-full object-cover transition-transform duration-700 group-hover:scale-110"
                                            alt=""
                                            loading="lazy"
                                        />
                                    </div>
                                @endforeach

                                {{-- completa 2x2 se vier menos de 4 --}}
                                @for ($i = $imgCount; $i < 4; $i++)
                                    @if ($imgCount > 1)
                                        <img
                                            src="{{ $itemPlaceholder }}"
                                            class="h-[200px] w-full object-cover"
                                            alt=""
                                            loading="lazy"
                                        />

                                    @endif
                                @endfor
                            </div>
                        @else
                            <img
                                src="{{ $collection->cover_url }}"
                                class="h-[400px] w-full object-cover"
                                alt=""
                                loading="lazy"
                            />
                        @endif
                    @endif

                    {{-- bottom gradient for text legibility --}}
                    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>

                    {{-- badges --}}
                    <div class="absolute left-3 top-3 flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-zinc-700 ring-1 ring-zinc-200/60">
                            {{ $filesCount }} {{ Str::plural(t('file'), $filesCount) }}
                        </span>
                        <span class="inline-flex items-center rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-zinc-700 ring-1 ring-zinc-200/60 uppercase">
                            {{ $type }}
                        </span>
                    </div>

                    {{-- title on image --}}
                    <div class="absolute inset-x-0 bottom-0 p-4">
                        <h3 class="text-base md:text-lg font-semibold tracking-tight text-white drop-shadow-md line-clamp-1">
                            {{ $collection->name }}
                        </h3>
                        <p class="mt-1 text-xs text-white/80 line-clamp-1">
                            {{ $collection->categories->pluck('name')->take(2)->join(' • ') }}
                            @if ($collection->categories->count() > 2)
                                • +{{ $collection->categories->count() - 2 }}
                            @endif
                        </p>
                    </div>
                </div>

            </a>
        </div>

    @empty
        <div class="col-span-full flex flex-col items-center justify-center py-20 text-zinc-500 dark:text-zinc-100">
            <flux:icon name="layers-2" class="w-10 h-10 mb-3 text-accent dark:text-zinc-100" />
            <p class="text-lg text-zinc-600 dark:text-zinc-50 font-semibold">{{ t('No collection found.') }}</p>
            <p class="text-sm text-zinc-500 dark:text-zinc-200">{{ t('The selected filter has no collections.') }}</p>
        </div>
    @endforelse
</div>
