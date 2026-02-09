<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">{{ t('Translations') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-300">{{ t('Edit site texts in PT-BR and EN.') }}</p>
            </div>
            <flux:button wire:click="openCreate">{{ t('New translation') }}</flux:button>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <flux:input class="w-full sm:max-w-xs" placeholder="{{ t('Search by key or value...') }}" wire:model.live="search" />
            <x-select
                wire:model.live="locale"
                :options="[
                    ['id' => 'pt_BR', 'name' => 'PT-BR'],
                    ['id' => 'en', 'name' => 'EN'],
                ]"
                option-label="name"
                option-value="id"
            />
        </div>

        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-900/30">
                    <tr class="text-left">
                        <th class="p-3">{{ t('Key') }}</th>
                        <th class="p-3">{{ t('Value') }}</th>
                        <th class="p-3">Locale</th>
                        <th class="p-3">{{ t('Active') }}</th>
                        <th class="p-3 w-40"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($translations as $row)
                        <tr class="border-t border-zinc-100 dark:border-zinc-900">
                            <td class="p-3 text-xs text-zinc-500 dark:text-zinc-400">{{ $row->key }}</td>
                            <td class="p-3">{{ \Illuminate\Support\Str::limit($row->value, 80) }}</td>
                            <td class="p-3">{{ $row->locale }}</td>
                            <td class="p-3">{{ $row->is_active ? t('Yes') : t('No') }}</td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="outline" wire:click="openEdit({{ $row->id }})">{{ t('Edit') }}</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-6 text-center text-zinc-500 dark:text-zinc-300" colspan="5">{{ t('No translations.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $translations->links() }}

        <flux:modal wire:model="showModal" class="max-w-3xl">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ $editingId ? t('Edit translation') : t('New translation') }}</flux:heading>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <flux:label>{{ t('Key') }}</flux:label>
                        <flux:input wire:model.live="form.key" />
                        @error('form.key') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <flux:label>{{ t('Locale') }}</flux:label>
                        <x-select
                            wire:model.live="form.locale"
                            :options="[
                                ['id' => 'pt_BR', 'name' => 'PT-BR'],
                                ['id' => 'en', 'name' => 'EN'],
                            ]"
                            option-label="name"
                            option-value="id"
                        />
                        @error('form.locale') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <flux:label>{{ t('Value') }}</flux:label>
                    <flux:textarea rows="6" wire:model.live="form.value" />
                    @error('form.value') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="flex items-center gap-2">
                    <flux:switch wire:model.live="form.is_active" />
                    <span class="text-sm text-zinc-600 dark:text-zinc-300">{{ t('Active') }}</span>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button variant="outline" wire:click="$set('showModal', false)">{{ t('Cancel') }}</flux:button>
                    <flux:button wire:click="save">{{ t('Save') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</div>
