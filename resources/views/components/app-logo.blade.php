<div
    x-data="{ loading: false }"
    x-on:livewire:navigating.window="loading = true"
    x-on:livewire:navigated.window="loading = false"
    class="m-auto"
>
    <a href="{{ route('home') }}"
       wire:navigate
       :class="loading ? 'pointer-events-none opacity-80' : ''"
       class="group flex flex-col items-start select-none cursor-pointer
              transition-all duration-300">

        {{-- Linha da logo --}}
        <div class="flex items-center  ">
            {{-- Spinner --}}
            <span x-show="loading" x-cloak
                  class="inline-flex items-center justify-center">
                <svg class="h-4 w-4 animate-spin text-zinc-500 dark:text-zinc-300"
                     viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z"></path>
                </svg>
            </span>

            {{-- Club --}}
            <span class="hidden md:inline text-2xl md:text-5xl font-black tracking-tight
                         text-accent transition-colors duration-300
                         group-hover:text-accent/90">
                Club
            </span>

            {{-- Step --}}
            <span class="relative font-black tracking-tight text-accent
                         text-3xl md:text-5xl
                         transition-all duration-300
                         group-hover:tracking-wide group-hover:scale-[1.03]">
                Step

            </span>
        </div>

        {{-- Subtítulo --}}
        <span class="mt-0.5 mx-auto text-[11px] md:text-xs
                     text-zinc-400 tracking-wide">
            {{ t('Sites • Collections • Arts') }}
        </span>
    </a>
</div>
