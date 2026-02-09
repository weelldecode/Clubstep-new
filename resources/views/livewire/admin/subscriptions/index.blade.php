<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">{{ t('Subscriptions') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-300">{{ t('Manage subscription status and period.') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <flux:input class="w-full" placeholder="{{ t('Search by user, email, plan, or status...') }}" wire:model.live="search" />
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
                        <th class="p-3">{{ t('User') }}</th>
                        <th class="p-3">{{ t('Plan') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('status')">{{ t('Status') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('started_at')">{{ t('Start') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('expires_at')">{{ t('Due date') }}</th>
                        <th class="p-3">{{ t('Payments') }}</th>
                        <th class="w-40 p-3"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($subscriptions as $subscription)
                        <tr class="border-t border-zinc-100 dark:border-zinc-900">
                            <td class="p-3">
                                <div class="font-medium">{{ $subscription->user?->name ?? '-' }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $subscription->user?->email ?? '-' }}</div>
                            </td>
                            <td class="p-3">{{ $subscription->plan?->name ?? '-' }}</td>
                            <td class="p-3">{{ $subscription->status }}</td>
                            <td class="p-3">{{ $subscription->started_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="p-3">{{ $subscription->expires_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="p-3">{{ $subscription->payments_count }}</td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="outline" wire:click="openStatusModal({{ $subscription->id }})">{{ t('Status') }}</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-6 text-center text-zinc-500 dark:text-zinc-300" colspan="7">{{ t('No subscriptions.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $subscriptions->links() }}

        <flux:modal wire:model="showStatusModal" class="min-w-lg">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ t('Update Subscription') }}</flux:heading>
                </div>

                <div class="grid gap-3">
                    <div>
                        <flux:label>{{ t('Status') }}</flux:label>
                        <x-select
                            wire:model.live="statusForm.status"
                            placeholder="{{ t('Select the status') }}"
                            :options="$statusOptions"
                            option-label="name"
                            option-value="id"
                        />
                        @error('statusForm.status') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <flux:label>{{ t('Start') }}</flux:label>
                            <flux:input type="date" wire:model.live="statusForm.started_at" />
                            @error('statusForm.started_at') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <flux:label>{{ t('Due date') }}</flux:label>
                            <flux:input type="date" wire:model.live="statusForm.expires_at" />
                            @error('statusForm.expires_at') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button variant="outline" wire:click="$set('showStatusModal', false)">{{ t('Cancel') }}</flux:button>
                    <flux:button wire:click="saveStatus">{{ t('Save') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</div>
