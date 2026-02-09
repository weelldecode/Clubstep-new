<div class="min-h-screen px-4 py-8 md:px-8">
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-zinc-900 dark:text-white">{{ t('Cart') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ t('Review your sites before checkout.') }}</p>
        </div>

        @if (empty($items))
            <div class="rounded-2xl border border-zinc-200/70 bg-white/80 p-10 text-center text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900/70 dark:text-zinc-300">
                {{ t('Your cart is empty.') }}
            </div>
        @else
            <div class="rounded-2xl border border-zinc-200/70 bg-white/80 shadow-[0_20px_55px_-45px_rgba(0,0,0,0.35)] dark:border-zinc-800 dark:bg-zinc-900/70">
                <div class="divide-y divide-zinc-200/60 dark:divide-zinc-800">
                    @foreach ($items as $cartItem)
                        <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-4">
                                <img
                                    src="{{ $cartItem->item?->preview_url ?? asset('images/placeholders/item-default.svg') }}"
                                    class="h-20 w-28 rounded-xl border border-zinc-200/60 object-cover dark:border-zinc-800"
                                    alt="{{ $cartItem->item?->name ?? t('Item') }}"
                                />
                                <div>
                                    <p class="text-sm font-bold text-zinc-900 dark:text-white">
                                        {{ $cartItem->item?->name ?? t('Item') }}
                                    </p>
                                    <p class="text-xs uppercase text-zinc-500 dark:text-zinc-400">
                                        {{ $cartItem->item?->type ?? '-' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ t('Quantity') }}: 1
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                        R$ {{ number_format((float) $cartItem->price, 2, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ t('Total') }}: R$ {{ number_format((float) $cartItem->price * (int) $cartItem->quantity, 2, ',', '.') }}
                                    </p>
                                </div>
                                <flux:button variant="outline" wire:click="removeItem({{ $cartItem->item_id }})">{{ t('Remove') }}</flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col items-end gap-3">
                <div class="text-right">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ t('Subtotal') }}</p>
                    <p class="text-2xl font-black text-zinc-900 dark:text-white">R$ {{ number_format((float) $total, 2, ',', '.') }}</p>
                </div>
                <flux:button variant="primary" wire:click="checkout">{{ t('Checkout') }}</flux:button>
            </div>
        @endif
    </div>
</div>
