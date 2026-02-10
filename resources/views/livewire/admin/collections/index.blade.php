<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">{{ t('Collections') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-100">{{ t('Manage collections and their items.') }}</p>
            </div>

            <flux:button wire:click="openCreate">{{ t('New Collection') }}</flux:button>
        </div>

        <div class="flex items-center gap-3">
            <flux:input class="w-full" placeholder="{{ t('Search by name or slug...') }}" wire:model.live="search" />
        </div>

        <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-900/30">
                    <tr class="text-left">
                        <th class="p-3 cursor-pointer" wire:click="toggleSort('name')">{{ t('Name') }}</th>
                        <th class="p-3 cursor-pointer" wire:click="toggleSort('slug')">{{ t('Slug') }}</th>
                        <th class="p-3">{{ t('Items') }}</th>
                        <th class="p-3 cursor-pointer" wire:click="toggleSort('status')">{{ t('Status') }}</th>
                        <th class="p-3 cursor-pointer" wire:click="toggleSort('visibility')">{{ t('Visibility') }}</th>
                        <th class="p-3 w-40"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($collections as $c)
                        <tr class="border-t border-zinc-100 dark:border-zinc-900">
                            <td class="p-3 font-medium">{{ $c->name }}</td>
                            <td class="p-3 text-zinc-500 dark:text-zinc-400">{{ $c->slug }}</td>
                            <td class="p-3">{{ $c->items_count }}</td>
                            <td class="p-3">{{ $c->status instanceof BackedEnum ? $c->status->value : $c->status }}</td>
                            <td class="p-3">{{ $c->visibility instanceof BackedEnum ? $c->visibility->value : $c->visibility }}</td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="outline" href="{{ route('admin.collections.items', $c) }}">
                                        {{ t('Items') }}
                                    </flux:button>
                                    <flux:button variant="outline" wire:click="openEdit({{ $c->id }})">{{ t('Edit') }}</flux:button>
                                    <flux:button variant="danger" wire:click="delete({{ $c->id }})"
                                        wire:confirm="{{ t('Are you sure?') }}">{{ t('Delete') }}</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="p-6 text-center text-zinc-500 dark:text-zinc-100" colspan="6">{{ t('No collections.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $collections->links() }}

        {{-- Modal --}}
        <flux:modal wire:model="showModal" class="min-w-xl">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ $editingId ? t('Edit Collection') : t('New Collection') }}</flux:heading>
                </div>

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

                    <div>
                        <flux:label>{{ t('Description') }}</flux:label>
                        <flux:textarea wire:model.live="form.description" rows="4" />
                        @error('form.description') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
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
                    <div class="grid gap-2">
                        <flux:label>{{ t('Author') }}</flux:label>

                        <flux:input
                            placeholder="{{ t('Search user by name or email...') }}"
                            wire:model.live="userSearch"
                        />

                        <x-select
                            wire:model.live="form.user_id"
                            placeholder="{{ t('Select the author') }}"
                            :options="$userOptions"
                            option-label="name"
                            option-value="id"
                        />

                        @error('form.user_id')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <flux:label>{{ t('Status') }}</flux:label>
                            <x-select
                                wire:model.live="form.status"
                                placeholder="{{ t('Select the status') }}"
                                :options="$statusOptions"
                                option-label="name"
                                option-value="id"
                            />
                            @error('form.status') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <flux:label>{{ t('Visibility') }}</flux:label>

                            <x-select
                                wire:model.live="form.visibility"
                                placeholder="{{ t('Select visibility') }}"
                                :options="$visibilityOptions"
                                option-label="name"
                                option-value="id"
                            />
                            @error('form.visibility') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div>
                        <flux:label>{{ t('Categories') }}</flux:label>
                        <div class="mt-2 max-h-48 overflow-auto rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                @forelse($categoryOptions as $cat)
                                    <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-200">
                                        <input
                                            type="checkbox"
                                            wire:model.live="form.category_ids"
                                            value="{{ $cat->id }}"
                                            class="rounded border-zinc-300 text-accent focus:ring-accent"
                                        />
                                        <span>{{ $cat->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('No categories available.') }}</p>
                                @endforelse
                            </div>
                        </div>
                        @error('form.category_ids') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                        @error('form.category_ids.*') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <flux:label>{{ t('Tags') }}</flux:label>
                        <div class="mt-2 max-h-48 overflow-auto rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                @forelse($tagOptions as $tag)
                                    <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-200">
                                        <input
                                            type="checkbox"
                                            wire:model.live="form.tag_ids"
                                            value="{{ $tag->id }}"
                                            class="rounded border-zinc-300 text-accent focus:ring-accent"
                                        />
                                        <span>{{ $tag->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('No tags available.') }}</p>
                                @endforelse
                            </div>
                        </div>
                        @error('form.tag_ids') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                        @error('form.tag_ids.*') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
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
