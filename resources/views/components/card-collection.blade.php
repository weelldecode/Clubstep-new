@props(['colecao', 'index'])

@php
    $images = [];
    $type = $colecao->type ?? 'mockups'; // fallback
    $itemPlaceholder = asset('images/placeholders/item-default.svg');
    foreach ($colecao->items as $item) {
        $imgs = is_array($item->images) ? $item->images : json_decode($item->images, true);
        if ($imgs) {
            foreach ($imgs as $img) {
                $images[] = $img;
                if (count($images) === 4) break 2;
            }
        }
    }

    $imgCount = count($images);
    $filesCount = $colecao->items_count ?? $colecao->items->count();
@endphp

<div
    x-data="{ visible: false }"
    x-init="setTimeout(() => visible = true, {{ $index * 70 }})"
    :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-3'"
    class="opacity-0 translate-y-3 transition-all duration-500 ease-out"
>
    <a href="/collection/v/{{ $colecao->slug }}"
       class="group block rounded-2xl overflow-hidden border border-zinc-200/70 dark:border-zinc-800/80
              bg-white dark:bg-zinc-900 transition-all duration-300
              hover:-translate-y-1 hover:shadow-[0_20px_50px_-30px_rgba(0,0,0,0.45)]">

                  {{-- Hero / Mosaic --}}
                  <div class="relative">

                      {{-- TYPE = SITES → imagem única (hero esticado) --}}
                      @if ($type === 'sites')

                          @php
                              $hero = $images[0] ?? null;
                          @endphp

                          <div class="relative overflow-hidden">
                          @if ($hero)
                              <img
                                      src="{{ asset('storage/' . $hero) }}"
                                      class="aspect-[4/3] w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                      alt=""
                                  />
                          @else
                                  <img
                                      src="{{ $colecao->cover_url }}"
                                      class="aspect-[4/3] w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                      alt=""
                                  />
                              @endif
                          </div>

                      {{-- TYPE = MOCKUPS / COLLECTION → mosaico padrão --}}
                      @else

                          @if ($imgCount > 0)
                              <div class="grid gap-1 {{ $imgCount === 1 ? 'grid-cols-1' : 'grid-cols-2' }}">
                                  @foreach ($images as $img)
                                      <div class="relative overflow-hidden">
                                          <img
                                              src="{{ asset('storage/' . $img) }}"
                                              class="h-[200px] w-full object-cover transition-transform duration-700 group-hover:scale-110"
                                              alt=""
                                          />
                                      </div>
                                  @endforeach

                                  {{-- completa o grid 2x2 --}}
                                  @for ($i = $imgCount; $i < 4; $i++)
                                      @if($imgCount > 1)
                                          <img
                                              src="{{ $itemPlaceholder }}"
                                              class="h-[200px] w-full object-cover"
                                              alt=""
                                          />
                                      @endif
                                  @endfor
                              </div>
                          @else
                              <img
                                  src="{{ $colecao->cover_url }}"
                                  class="h-[400px] w-full object-cover"
                                  alt=""
                              />
                          @endif

                      @endif

                      {{-- overlay --}}
                      <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent opacity-95"></div>

                      {{-- badges --}}
                      <div class="absolute left-3 top-3 flex items-center gap-2">
                          <span class="inline-flex items-center rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-zinc-700 ring-1 ring-zinc-200/60">
                              {{ $filesCount }} {{ Str::plural(t('file'), $filesCount) }}
                          </span>

                          <span class="inline-flex items-center rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-zinc-700 ring-1 ring-zinc-200/60 uppercase">
                              {{ $type }}
                          </span>
                      </div>

                      {{-- título --}}
                      <div class="absolute inset-x-0 bottom-0 p-4">
                          <h3 class="text-base md:text-lg font-semibold tracking-tight text-white drop-shadow-md line-clamp-1">
                              {{ $colecao->name }}
                          </h3>

                          <div class="mt-1 flex items-center gap-2 text-xs text-white/80">
                              <span class="inline-flex items-center gap-1">
                                  <span class="h-1.5 w-1.5 rounded-full bg-accent"></span>
                                  {{ t('Recently updated') }}
                              </span>
                          </div>
                      </div>
                  </div>

    </a>
</div>
