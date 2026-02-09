<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">{{ t('Plans') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-300">{{ t('Create and manage subscription plans.') }}</p>
            </div>

            <flux:button wire:click="openCreate">{{ t('New Plan') }}</flux:button>
        </div>

        <div class="flex items-center gap-3">
            <flux:input class="w-full" placeholder="{{ t('Search by name or slug...') }}" wire:model.live="search" />
        </div>
        @if($setupError)
            <div class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-700/40 dark:bg-amber-900/20 dark:text-amber-300">
                {{ $setupError }}
            </div>
        @endif

        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-900/30">
                    <tr class="text-left">
                        <th class="cursor-pointer p-3" wire:click="toggleSort('name')">{{ t('Name') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('slug')">{{ t('Slug') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('price')">{{ t('Price') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('limit_download')">{{ t('Daily limit') }}</th>
                        <th class="p-3">{{ t('Features') }}</th>
                        <th class="w-40 p-3"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($plans as $plan)
                        <tr class="border-t border-zinc-100 dark:border-zinc-900">
                            <td class="p-3 font-medium">{{ $plan->name }}</td>
                            <td class="p-3 text-zinc-500 dark:text-zinc-400">{{ $plan->slug }}</td>
                            <td class="p-3">R$ {{ number_format((float) $plan->price, 2, ',', '.') }}</td>
                            <td class="p-3">{{ (int) $plan->limit_download }}</td>
                            <td class="p-3">{{ count($plan->features ?? []) }}</td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="outline" wire:click="openEdit({{ $plan->id }})">{{ t('Edit') }}</flux:button>
                                    <flux:button variant="danger" wire:click="delete({{ $plan->id }})" wire:confirm="{{ t('Are you sure?') }}">{{ t('Delete') }}</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-6 text-center text-zinc-500 dark:text-zinc-300" colspan="6">{{ t('No plans.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $plans->links() }}

        <flux:modal wire:model="showModal" class="min-w-xl">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ $editingId ? t('Edit Plan') : t('New Plan') }}</flux:heading>
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
                        <flux:textarea wire:model.live="form.description" rows="3" />
                        @error('form.description') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <flux:label>{{ t('Price') }}</flux:label>
                            <flux:input type="number" step="0.01" min="0" wire:model.live="form.price" />
                            @error('form.price') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <flux:label>{{ t('Daily download limit') }}</flux:label>
                            <flux:input type="number" min="0" wire:model.live="form.limit_download" />
                            @error('form.limit_download') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div>
                        <flux:label>{{ t('Features (one per line)') }}</flux:label>
                        <flux:textarea wire:model.live="form.features_text" rows="6" />
                        @error('form.features_text') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
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
