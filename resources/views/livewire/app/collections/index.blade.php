<div class="container mx-auto mt-8">
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp .6s ease-out both; }
        .delay-1 { animation-delay: .08s; }
        .delay-2 { animation-delay: .16s; }
        .delay-3 { animation-delay: .24s; }
    </style>
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-end md:justify-between fade-up">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">{{ t('Collections') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ t('Explore by type, format, and categories — with card or list view.') }}
            </p>
        </div>

        {{-- Mobile: botão filtros --}}
        <div class="flex items-center gap-2 md:hidden">
                <flux:button variant="outline" x-data @click="$dispatch('open-filters')">
                    <flux:icon.sliders-horizontal class="size-5" />
                    {{ t('Filters') }}
                </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Sidebar Desktop --}}
        <aside class="hidden lg:block lg:col-span-3 fade-up delay-1">
            <div class="sticky top-10">
                <div class="rounded-2xl border border-zinc-200/70 dark:border-zinc-700 bg-white/70 dark:bg-zinc-900/80 backdrop-blur p-5 shadow-[0_20px_60px_-50px_rgba(0,0,0,0.35)]">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold tracking-wide text-zinc-700 dark:text-zinc-100 flex items-center gap-2">
                            <flux:icon.sliders-horizontal class="size-5" />
                            {{ t('Filters') }}
                        </h2>

                        {{-- opcional: limpar filtros --}}
                        <button type="button"
                                wire:click="resetFilters"
                                class="text-xs font-semibold text-accent hover:underline">
                            {{ t('Clear') }}
                        </button>
                    </div>

                    <flux:separator class="my-4" />

                    <div class="space-y-6">
                        {{-- Tipo de Colecao --}}
                        <div x-data="{ open: true }">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex w-full items-center justify-between"
                            >
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ t('Collection type') }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Sites, Mockups, Arts') }}</p>
                                </div>

                                <flux:icon.chevron-down
                                    class="size-4 transition-transform duration-300"
                                    x-bind:class="open ? 'rotate-180' : ''"
                                />
                            </button>

                            <div x-show="open" x-transition x-cloak class="mt-3">
                                <flux:checkbox.group wire:model.live="selectedCollectionTypes" class="space-y-2">
                                    <flux:checkbox label="{{ t('Sites') }}" value="sites" />
                                    <flux:checkbox label="{{ t('Mockups') }}" value="mockups" />
                                    <flux:checkbox label="{{ t('Arts') }}" value="arts" />
                                </flux:checkbox.group>
                            </div>
                        </div>

                        <flux:separator />

                        {{-- Tipo de Conteúdo --}}
                        <div x-data="{ open: true }">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex w-full items-center justify-between"
                            >
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ t('Content type') }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Choose categories') }}</p>
                                </div>

                                <flux:icon.chevron-down
                                    class="size-4 transition-transform duration-300"
                                    x-bind:class="open ? 'rotate-180' : ''"
                                />
                            </button>

                            <div x-show="open" x-transition x-cloak class="mt-3">
                                <flux:checkbox.group wire:model.live="selectedCollectionCategories" class="space-y-2">
                                    @foreach ($allCategories as $category)
                                        <flux:checkbox label="{{ $category->name }}" value="{{ $category->id }}" />
                                    @endforeach
                                </flux:checkbox.group>
                            </div>
                        </div>

                        <flux:separator />

                        {{-- Formato --}}
                        <div x-data="{ open: true }">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex w-full items-center justify-between"
                            >
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ t('Format') }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Tags and formats') }}</p>
                                </div>

                                <flux:icon.chevron-down
                                    class="size-4 transition-transform duration-300"
                                    x-bind:class="open ? 'rotate-180' : ''"
                                />
                            </button>

                            <div x-show="open" x-transition x-cloak class="mt-3">
                                <flux:checkbox.group wire:model.live="selectedCollectionTags" class="space-y-2">
                                    @foreach ($allTags as $tag)
                                        <flux:checkbox label="{{ $tag->name }}" value="{{ $tag->id }}" />
                                    @endforeach
                                </flux:checkbox.group>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Conteúdo --}}
        <main class="lg:col-span-9 space-y-4 fade-up delay-2">
            {{-- Toolbar (sticky no desktop) --}}
            <div class="rounded-2xl border border-zinc-200/70 dark:border-zinc-700 bg-white/70 dark:bg-zinc-900/80 backdrop-blur p-4
                        md:top-24 z-10 shadow-[0_20px_60px_-50px_rgba(0,0,0,0.35)]">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

                    {{-- Busca --}}
                    <div class="flex-1">
                        <flux:input icon="magnifying-glass"
                                    placeholder="{{ t('Search collections...') }}"
                                    wire:model.live.debounce.500ms="search" />
                    </div>

                    <div class="flex items-center gap-2 md:gap-3">
                        {{-- Toggle view --}}
                        <div x-data="{ viewMode: @entangle('viewMode') }">
                            <flux:button wire:click="toggleViewMode" variant="outline">
                                <span x-text="viewMode === 'card' ? '{{ t('List') }}' : '{{ t('Cards') }}'"></span>
                            </flux:button>
                        </div>

                        {{-- Ordenação --}}
                        <div class="w-[220px] hidden md:block">
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

                        <flux:button wire:click="toggleSortDirection" variant="outline" title="{{ t('Toggle order') }}">
                            @if ($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        </flux:button>
                    </div>
                </div>

                {{-- Ordenação mobile --}}
                <div class="mt-3 md:hidden">
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

                {{-- Chips ativos (opcional) --}}
                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    @if(!empty($selectedCollectionTypes))
                        <span class="inline-flex items-center rounded-full bg-zinc-100/80 dark:bg-zinc-900 px-3 py-1 font-semibold text-zinc-700 dark:text-zinc-200 ring-1 ring-zinc-200/60 dark:ring-zinc-800">
                            {{ count($selectedCollectionTypes) }} {{ t('types') }}
                        </span>
                    @endif

                    @if(!empty($selectedCollectionCategories))
                        <span class="inline-flex items-center rounded-full bg-zinc-100/80 dark:bg-zinc-900 px-3 py-1 font-semibold text-zinc-700 dark:text-zinc-200 ring-1 ring-zinc-200/60 dark:ring-zinc-800">
                            {{ count($selectedCollectionCategories) }} {{ t('categories') }}
                        </span>
                    @endif

                    @if(!empty($selectedCollectionTags))
                        <span class="inline-flex items-center rounded-full bg-zinc-100/80 dark:bg-zinc-900 px-3 py-1 font-semibold text-zinc-700 dark:text-zinc-200 ring-1 ring-zinc-200/60 dark:ring-zinc-800">
                            {{ count($selectedCollectionTags) }} {{ t('formats') }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Views --}}
            <div x-data="{ viewMode: @entangle('viewMode') }" wire:ignore.self class="relative">
                <template x-if="viewMode === 'card'">
                    <div
                        x-show="viewMode === 'card'"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 transform translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform translate-y-2"
                        style="display:none;"
                    >
                        @include('livewire.app.collections.group.card-view')
                    </div>
                </template>

                <template x-if="viewMode === 'list'">
                    <div
                        x-show="viewMode === 'list'"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 transform translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform translate-y-2"
                        style="display:none;"
                    >
                        @include('livewire.app.collections.group.list-view')
                    </div>
                </template>
            </div>

            {{-- Paginação --}}
            <div class="pt-2">
                {{ $collections->links() }}
            </div>
        </main>
    </div>

    {{-- Drawer de filtros (mobile) --}}
    <div
        x-data="{ open:false }"
        x-on:open-filters.window="open = true"
        x-on:keydown.escape.window="open = false"
        class="lg:hidden"
    >
        <div x-show="open" x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="open=false" x-cloak></div>

        <div x-show="open" x-transition
             class="fixed right-0 top-0 z-50 h-full w-[90%] max-w-sm bg-white/90 dark:bg-zinc-950/90 border-l border-zinc-200/70 dark:border-zinc-800/70 p-5 overflow-y-auto backdrop-blur"
             x-cloak>

            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold tracking-wide text-zinc-700 dark:text-zinc-100 flex items-center gap-2">
                    <flux:icon.sliders-horizontal class="size-5" /> {{ t('Filters') }}
                </h2>
                <button class="text-sm text-zinc-500" @click="open=false">{{ t('Close') }}</button>
            </div>

            <flux:separator class="my-4" />

            {{-- Repete os filtros aqui (mobile) --}}
            <div class="space-y-6">
                <div>
                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-2">{{ t('Collection type') }}</p>
                    <flux:checkbox.group wire:model.live="selectedCollectionTypes" class="space-y-2">
                        <flux:checkbox label="{{ t('Sites') }}" value="sites" />
                        <flux:checkbox label="{{ t('Mockups') }}" value="mockups" />
                        <flux:checkbox label="{{ t('Arts') }}" value="arts" />
                    </flux:checkbox.group>
                </div>

                <flux:separator />

                <div>
                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-2">{{ t('Content type') }}</p>
                    <flux:checkbox.group wire:model.live="selectedCollectionCategories" class="space-y-2">
                        @foreach ($allCategories as $category)
                            <flux:checkbox label="{{ $category->name }}" value="{{ $category->id }}" />
                        @endforeach
                    </flux:checkbox.group>
                </div>

                <flux:separator />

                <div>
                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-2">{{ t('Format') }}</p>
                    <flux:checkbox.group wire:model.live="selectedCollectionTags" class="space-y-2">
                        @foreach ($allTags as $tag)
                            <flux:checkbox label="{{ $tag->name }}" value="{{ $tag->id }}" />
                        @endforeach
                    </flux:checkbox.group>
                </div>

                <div class="pt-4">
                    <flux:button class="w-full" variant="primary" @click="open=false">{{ t('Apply') }}</flux:button>
                </div>
            </div>
        </div>
    </div>
</div>
