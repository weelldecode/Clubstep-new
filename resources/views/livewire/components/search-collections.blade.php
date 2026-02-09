<div class="relative w-full max-w-2xl">
    <div class="flex gap-2 ">
        <!-- Select de categoria -->
        <div class=" w-[10rem]   z-20">
            <x-select  wire:model="selectedCategory" placeholder="Formato">
                @foreach($allCategories as $category)
                <x-select.option label="{{ $category->name }}" value="{{ $category->id }}" />
                @endforeach
            </x-select>
        </div>

        <!-- Campo de busca -->
        <div class="flex-1 relative">
            <flux:input type="text" icon="magnifying-glass"
                   wire:model.live="search" class="bg-zinc-50 dark:bg-zinc-700/60 text-sm "
                   placeholder="Digite o nome da coleção..."
            />

            <!-- Dropdown de resultados -->
            @if(!empty($results) && $search !== '')
                <ul class="absolute z-50 w-full bg-white dark:bg-zinc-700/70 rounded-lg border dark:border-zinc-700 rounded mt-5 max-h-64 overflow-y-auto shadow-lg">
                    @foreach($results as $collection)
                        <li class="px-3 py-3 hover:bg-gray-100 dark:hover:bg-zinc-700 dark:hover:text-white cursor-pointer"
                        >
                                <a  href="/collection/v/{{ $collection->slug }}">
                            {{ $collection->name }}
                            @if($collection->categories->isNotEmpty())
                                <span class="text-sm text-accent font-bold bg-accent/10 px-1 py-1 ml-2 rounded">
                                    {{ $collection->categories->pluck('name')->join(', ') }}
                                </span>
                            @endif
                                </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
