<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">{{ t('Orders') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-300">{{ t('Track one-time orders.') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <flux:input class="w-full" placeholder="{{ t('Search by user, email, status, or id...') }}" wire:model.live="search" />
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
                        <th class="p-3">{{ t('Order') }}</th>
                        <th class="p-3">{{ t('User') }}</th>
                        <th class="p-3">{{ t('Items') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('total_amount')">{{ t('Total') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('status')">{{ t('Status') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('created_at')">{{ t('Created at') }}</th>
                        <th class="w-40 p-3"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $order)
                        <tr class="border-t border-zinc-100 dark:border-zinc-900">
                            <td class="p-3 font-semibold">#{{ $order->id }}</td>
                            <td class="p-3">
                                <div class="font-medium">{{ $order->user?->name ?? '-' }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $order->user?->email ?? '-' }}</div>
                            </td>
                            <td class="p-3">
                                {{ $order->items_count ?? 0 }}
                            </td>
                            <td class="p-3">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
                            <td class="p-3">{{ $order->status }}</td>
                            <td class="p-3">{{ $order->created_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="outline" wire:click="openItemsModal({{ $order->id }})">{{ t('Items') }}</flux:button>
                                    <flux:button variant="outline" wire:click="openStatusModal({{ $order->id }})">{{ t('Status') }}</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-6 text-center text-zinc-500 dark:text-zinc-300" colspan="7">{{ t('No orders.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $orders->links() }}

        <flux:modal wire:model="showStatusModal" class="min-w-lg">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ t('Update Order') }}</flux:heading>
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
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button variant="outline" wire:click="$set('showStatusModal', false)">{{ t('Cancel') }}</flux:button>
                    <flux:button wire:click="saveStatus">{{ t('Save') }}</flux:button>
                </div>
            </div>
        </flux:modal>

        <flux:modal wire:model="showItemsModal" class="min-w-lg">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ t('Order items') }}</flux:heading>
                </div>

                @if ($itemsOrder)
                    <div class="space-y-2 text-sm">
                        @forelse ($itemsOrder->items as $orderItem)
                            <div class="flex items-center justify-between rounded-lg border border-zinc-200/70 bg-white/70 p-3 dark:border-zinc-800 dark:bg-zinc-900/70">
                                <div>
                                    <div class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $orderItem->item?->name ?? t('Item') }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Qty') }}: {{ $orderItem->quantity }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold">R$ {{ number_format((float) $orderItem->total, 2, ',', '.') }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Unit') }}: R$ {{ number_format((float) $orderItem->price, 2, ',', '.') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-zinc-500 dark:text-zinc-300">{{ t('No items.') }}</div>
                        @endforelse
                    </div>
                @else
                    <div class="text-sm text-zinc-500 dark:text-zinc-300">{{ t('Select an order.') }}</div>
                @endif

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button variant="outline" wire:click="$set('showItemsModal', false)">{{ t('Close') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</div>
