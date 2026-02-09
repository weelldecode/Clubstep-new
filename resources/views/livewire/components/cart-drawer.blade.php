<div class="flex h-[90vh] flex-col text-sm">
    <div>
        <h2 class="text-base font-black tracking-tight text-zinc-900 dark:text-zinc-100">{{ t('Cart') }}</h2>
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Single purchase of sites.') }}</p>
    </div>

    @guest
        <div class="mt-4 rounded-xl border border-zinc-200/70 bg-white/80 p-4 text-sm text-zinc-600 dark:border-zinc-800 dark:bg-zinc-900/70 dark:text-zinc-300">
            {{ t('Log in to see your cart.') }}
        </div>
        <div class="mt-4">
            <flux:button href="{{ route('login') }}" variant="primary" class="w-full">{{ t('Log in') }}</flux:button>
        </div>
    @else
        @if ($items->isEmpty())
            <div class="mt-4 rounded-xl border border-zinc-200/70 bg-white/80 p-4 text-sm text-zinc-600 dark:border-zinc-800 dark:bg-zinc-900/70 dark:text-zinc-300">
                {{ t('Your cart is empty.') }}
            </div>
        @else
            <div class="mt-3 flex-1 space-y-2 overflow-y-auto pr-1">
                @foreach ($items as $cartItem)
                    <div class="flex items-center gap-3 rounded-lg border border-zinc-200/60 bg-white/70 p-2.5 dark:border-zinc-800 dark:bg-zinc-900/60">
                        <img
                            src="{{ $cartItem->item?->preview_url ?? asset('images/placeholders/item-default.svg') }}"
                            class="h-12 w-16 rounded-md border border-zinc-200/60 object-cover dark:border-zinc-800"
                            alt="{{ $cartItem->item?->name ?? t('Item') }}"
                        />
                        <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $cartItem->item?->name ?? t('Item') }}
                                </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                R$ {{ number_format((float) $cartItem->price, 2, ',', '.') }}
                            </div>
                            <div class="mt-2 flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                                {{ t('Quantity') }}: 1
                                <flux:button size="xs" variant="outline" wire:click="removeItem({{ $cartItem->item_id }})">{{ t('Remove') }}</flux:button>
                            </div>
                        </div>
                        <div class="text-right text-[11px] text-zinc-500 dark:text-zinc-400">
                            {{ t('Total') }}
                            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                                R$ {{ number_format((float) $cartItem->price * (int) $cartItem->quantity, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3 border-t border-zinc-200/70 pt-3 dark:border-zinc-800">
                <div class="flex items-center justify-between rounded-lg border border-zinc-200/60 bg-white/70 px-3 py-2 text-sm font-semibold text-zinc-800 dark:border-zinc-800 dark:bg-zinc-900/60 dark:text-zinc-100">
                    <span>{{ t('Total') }}</span>
                    <span>R$ {{ number_format((float) $total, 2, ',', '.') }}</span>
                </div>

                <div class="mt-2 grid grid-cols-2 gap-2">
                    <flux:button variant="primary" class="w-full" wire:click="checkout">{{ t('Checkout') }}</flux:button>
                    <flux:button href="{{ route('cart.index') }}" variant="outline" class="w-full">{{ t('View cart') }}</flux:button>
                </div>
            </div>
        @endif
    @endguest
</div>
