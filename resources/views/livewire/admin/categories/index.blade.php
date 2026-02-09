<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">{{ t('Categories') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-300">{{ t('Manage categories for collections and items.') }}</p>
            </div>

            <flux:button wire:click="openCreate">{{ t('New Category') }}</flux:button>
        </div>

        <div class="flex items-center gap-3">
            <flux:input class="w-full" placeholder="{{ t('Search by name or slug...') }}" wire:model.live="search" />
        </div>

        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-900/30">
                    <tr class="text-left">
                        <th class="p-3">{{ t('Image') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('name')">{{ t('Name') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('slug')">{{ t('Slug') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('type')">{{ t('Type') }}</th>
                        <th class="p-3">{{ t('Parent') }}</th>
                        <th class="p-3">{{ t('Collections') }}</th>
                        <th class="w-40 p-3"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $category)
                        <tr class="border-t border-zinc-100 dark:border-zinc-900">
                            <td class="p-3">
                                <img
                                    src="{{ $category->image_url }}"
                                    alt="{{ $category->name }}"
                                    class="h-10 w-10 rounded-md object-cover"
                                />
                            </td>
                            <td class="p-3 font-medium">{{ $category->name }}</td>
                            <td class="p-3 text-zinc-500 dark:text-zinc-400">{{ $category->slug }}</td>
                            <td class="p-3">{{ $category->type ?? '-' }}</td>
                            <td class="p-3">{{ $category->parent?->name ?? '-' }}</td>
                            <td class="p-3">{{ $category->collections_count }}</td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="outline" wire:click="openEdit({{ $category->id }})">{{ t('Edit') }}</flux:button>
                                    <flux:button variant="danger" wire:click="delete({{ $category->id }})" wire:confirm="{{ t('Are you sure?') }}">{{ t('Delete') }}</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-6 text-center text-zinc-500 dark:text-zinc-300" colspan="7">{{ t('No categories.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $categories->links() }}

        <flux:modal wire:model="showModal" class="min-w-xl">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ $editingId ? t('Edit Category') : t('New Category') }}</flux:heading>
                </div>

                <div class="grid gap-3">
                    <div>
                        <flux:label>{{ t('Name') }}</flux:label>
                        <flux:input wire:model.live="form.name" />
                        @error('form.name') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <flux:label>{{ t('Slug') }}</flux:label>
                        <flux:input wire:model.live="form.slug" />
                        @error('form.slug') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <flux:label>{{ t('Description') }}</flux:label>
                        <flux:textarea wire:model.live="form.description" rows="4" />
                        @error('form.description') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <flux:label>{{ t('Type') }}</flux:label>
                            <x-select
                                wire:model.live="form.type"
                                placeholder="{{ t('Select the type') }}"
                                :options="$typeOptions"
                                option-label="name"
                                option-value="id"
                            />
                            @error('form.type') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <flux:label>{{ t('Parent category') }}</flux:label>
                            <x-select
                                wire:model.live="form.parent_id"
                                placeholder="{{ t('No parent') }}"
                                :options="$parentOptions"
                                option-label="name"
                                option-value="id"
                            />
                            @error('form.parent_id') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div>
                        <flux:label>{{ t('Category image') }}</flux:label>
                        <div class="mt-2 flex items-center gap-4">
                            <img
                                src="{{ !empty($form['image']) ? asset('storage/' . $form['image']) : asset('images/placeholders/category-default.svg') }}"
                                alt="{{ t('Current image') }}"
                                class="h-14 w-14 rounded-md object-cover"
                            />
                            <div class="flex-1">
                                <flux:input type="file" wire:model.live="form.image_upload" />
                                @error('form.image_upload') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        @if(!empty($form['image_upload']))
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Preview') }}:</span>
                                <img
                                    src="{{ $form['image_upload']->temporaryUrl() }}"
                                    alt="{{ t('Preview') }}"
                                    class="h-14 w-14 rounded-md object-cover"
                                />
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
</div>
