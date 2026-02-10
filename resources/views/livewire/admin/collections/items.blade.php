<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <div class="text-sm text-zinc-500">
                <a class="underline" href="{{ route('admin.collections.index') }}">{{ t('Collections') }}</a>
                <span class="mx-2">/</span>
                <span class="text-zinc-700 dark:text-zinc-200">{{ $collection->name }}</span>
            </div>
            <h1 class="text-2xl font-bold">{{ t('Items') }}</h1>
        </div>

        <flux:button wire:click="openCreate">{{ t('New Item') }}</flux:button>
    </div>

    <flux:input placeholder="{{ t('Search item...') }}" wire:model.live="search" />

    <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900/30">
                <tr class="text-left">
                    <th class="p-3 cursor-pointer" wire:click="toggleSort('name')">{{ t('Name') }}</th>
                    <th class="p-3 cursor-pointer" wire:click="toggleSort('slug')">{{ t('Slug') }}</th>
                    <th class="p-3">{{ t('Type') }}</th>
                    <th class="p-3">{{ t('Price') }}</th>
                    <th class="p-3">{{ t('Preview') }}</th>
                    <th class="p-3 w-40"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $it)
                    <tr class="border-t border-zinc-100 dark:border-zinc-900">
                        <td class="p-3 font-medium">{{ $it->name }}</td>
                        <td class="p-3 text-zinc-500">{{ $it->slug }}</td>
                        <td class="p-3 text-xs uppercase text-zinc-500">{{ $it->type }}</td>
                        <td class="p-3">R$ {{ number_format((float) $it->price, 2, ',', '.') }}</td>
                        <td class="p-3">
                            <img class="h-10 w-16 rounded object-cover border border-zinc-200 dark:border-zinc-800"
                                 src="{{ $it->preview_url }}" />
                        </td>
                        <td class="p-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button variant="outline" wire:click="openEdit({{ $it->id }})">{{ t('Edit') }}</flux:button>
                                <flux:button variant="danger" wire:click="delete({{ $it->id }})"
                                    wire:confirm="{{ t('Are you sure?') }}">{{ t('Delete') }}</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-zinc-500" colspan="6">{{ t('No items.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links() }}

    <flux:modal wire:model="showModal" class="max-w-xl">
        <div class="space-y-4">
            <flux:heading size="lg">{{ $editingId ? t('Edit Item') : t('New Item') }}</flux:heading>

            <div class="grid gap-3">
                <div>
                    <flux:label>{{ t('Name') }}</flux:label>
                    <flux:input wire:model.live="form.name" />
                    @error('form.name') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <flux:label>{{ t('Slug') }}</flux:label>
                    <flux:input wire:model.live="form.slug" />
                    @error('form.slug') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <flux:label>{{ t('Type') }}</flux:label>
                        <x-select
                            wire:model.live="form.type"
                            placeholder="{{ t('Select the type') }}"
                            :options="[
                                ['id' => 'mockups', 'name' => t('Mockups')],
                                ['id' => 'arts', 'name' => t('Arts')],
                                ['id' => 'sites', 'name' => t('Sites')],
                            ]"
                            option-label="name"
                            option-value="id"
                        />
                        @error('form.type') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <flux:label>{{ t('Price (R$)') }}</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model.live="form.price" />
                        @error('form.price') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <flux:label>{{ t('Template ZIP') }}</flux:label>

                    <label class="group cursor-pointer block rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50/60 dark:bg-zinc-900/30 p-4 hover:bg-zinc-100/60 dark:hover:bg-zinc-900/60 transition">
                        <input type="file"
                               class="hidden"
                               wire:model="form.file_upload"
                               accept=".zip,application/zip" />
                        <div class="flex items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="text-sm font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ t('Select ZIP file') }}
                                </div>
                                <div class="text-xs text-zinc-500">
                                    {{ t('ZIP • up to 50MB') }}
                                </div>
                            </div>

                            <div class="text-xs px-3 py-1 rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950">
                                {{ t('Choose file') }}
                            </div>
                        </div>
                    </label>

                    @error('form.file_upload')
                        <div class="text-xs text-red-500">{{ $message }}</div>
                    @enderror

                    <div class="space-y-2">
                        <flux:label>{{ t('External link (optional)') }}</flux:label>
                        <flux:input wire:model.live="form.file_url" placeholder="https://..." />
                        @error('form.file_url') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>

                    @if (!empty($form['file_url']))
                        <div class="text-xs text-zinc-500">
                            {{ t('Current file') }}: {{ basename($form['file_url']) }}
                        </div>
                    @endif
                </div>

                <div class="space-y-2">
                    <flux:label>{{ t('Cover') }}</flux:label>

                    <label class="group cursor-pointer block rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50/60 dark:bg-zinc-900/30 p-4 hover:bg-zinc-100/60 dark:hover:bg-zinc-900/60 transition">
                        <input type="file"
                               class="hidden"
                               wire:model="form.image_upload"
                               accept="image/*" />

                        <div class="flex items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="text-sm font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ t('Select cover image') }}
                                </div>
                                <div class="text-xs text-zinc-500">
                                    {{ t('PNG/JPG/WebP • up to 4MB') }}
                                </div>
                            </div>

                            <div class="text-xs px-3 py-1 rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950">
                                {{ t('Choose file') }}
                            </div>
                        </div>
                    </label>

                    @error('form.image_path')
                        <div class="text-xs text-red-500">{{ $message }}</div>
                    @enderror

                    <div class="mt-2">
                        @if ($form['image_upload'])
                            <img src="{{ $form['image_upload']->temporaryUrl() }}"
                                 class="h-28 w-full rounded-lg object-cover border" />
                        @elseif ($form['image_path'])
                            <img src="{{ asset('storage/' . $form['image_path']) }}"
                                 class="h-28 w-full rounded-lg object-cover border" />
                        @else
                            <img src="{{ asset('images/placeholders/item-default.svg') }}"
                                 class="h-28 w-full rounded-lg object-cover border" />
                        @endif
                    </div>
                </div>
                <div class="space-y-2">
                    <flux:label>{{ t('Gallery') }}</flux:label>

                    <label class="group cursor-pointer block rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50/60 dark:bg-zinc-900/30 p-4 hover:bg-zinc-100/60 dark:hover:bg-zinc-900/60 transition">
                        <input type="file"
                               class="hidden"
                               wire:model="form.gallery_uploads"
                               accept="image/*"
                               multiple />
                        <div class="flex items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="text-sm font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ t('Add images to gallery') }}
                                </div>
                                <div class="text-xs text-zinc-500">
                                    {{ t('Select multiple • up to 4MB each') }}
                                </div>
                            </div>

                            <div class="text-xs px-3 py-1 rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950">
                                {{ t('Choose files') }}
                            </div>
                        </div>
                    </label>

                    @error('form.galleryFiles')
                        <div class="text-xs text-red-500">{{ $message }}</div>
                    @enderror
                    @error('form.galleryFiles.*')
                        <div class="text-xs text-red-500">{{ $message }}</div>
                    @enderror

                    {{-- Preview dos novos uploads --}}
                    @if (!empty($form['gallery_uploads']))
                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($form['gallery_uploads'] as $f)
                                <img src="{{ $f->temporaryUrl() }}"
                                     class="h-20 w-full rounded object-cover border" />
                            @endforeach
                        </div>
                    @endif

                    {{-- Preview do que já existe --}}
                    @if (!empty($form['images']))
                        <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach ($form['images'] as $i => $img)
                                <div class="relative group">
                                    <img class="h-20 w-full rounded-lg object-cover border border-zinc-200 dark:border-zinc-800"
                                         src="{{ asset('storage/' . $img) }}">

                                    <button type="button"
                                            wire:click="removeGalleryImage({{ $i }})"
                                            class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition bg-black/70 text-white text-xs px-2 py-1 rounded">
                                        {{ t('Remove') }}
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button variant="outline" wire:click="$set('showModal', false)">{{ t('Cancel') }}</flux:button>
                <flux:button wire:click="save">{{ t('Save') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
