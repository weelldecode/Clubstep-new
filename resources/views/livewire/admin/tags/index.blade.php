<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">{{ t('Tags') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-300">{{ t('Manage tags for collections and items.') }}</p>
            </div>

            <flux:button wire:click="openCreate">{{ t('New Tag') }}</flux:button>
        </div>

        <div class="flex items-center gap-3">
            <flux:input class="w-full" placeholder="{{ t('Search by name or slug...') }}" wire:model.live="search" />
        </div>

        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-900/30">
                    <tr class="text-left">
                        <th class="cursor-pointer p-3" wire:click="toggleSort('name')">{{ t('Name') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('slug')">{{ t('Slug') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('type')">{{ t('Type') }}</th>
                        <th class="p-3">{{ t('Parent') }}</th>
                        <th class="p-3">{{ t('Collections') }}</th>
                        <th class="w-40 p-3"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($tags as $tag)
                        <tr class="border-t border-zinc-100 dark:border-zinc-900">
                            <td class="p-3 font-medium">{{ $tag->name }}</td>
                            <td class="p-3 text-zinc-500 dark:text-zinc-400">{{ $tag->slug }}</td>
                            <td class="p-3">{{ $tag->type ?? '-' }}</td>
                            <td class="p-3">{{ $tag->parent?->name ?? '-' }}</td>
                            <td class="p-3">{{ (int) $tag->collections_count + (int) $tag->legacy_collections_count }}</td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="outline" wire:click="openEdit({{ $tag->id }})">{{ t('Edit') }}</flux:button>
                                    <flux:button variant="danger" wire:click="delete({{ $tag->id }})" wire:confirm="{{ t('Are you sure?') }}">{{ t('Delete') }}</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-6 text-center text-zinc-500 dark:text-zinc-300" colspan="6">{{ t('No tags.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $tags->links() }}

        <flux:modal wire:model="showModal" class="min-w-xl">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ $editingId ? t('Edit Tag') : t('New Tag') }}</flux:heading>
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
                            <flux:label>{{ t('Parent tag') }}</flux:label>
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
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button variant="outline" wire:click="$set('showModal', false)">{{ t('Cancel') }}</flux:button>
                    <flux:button wire:click="save">{{ t('Save') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</div>
