<div x-data="{ open: @json($showSubscriptionModal) }" x-show="open" x-transition
    class="fixed inset-0  bg-opacity-30 bg-black/40 backdrop-blur flex items-center justify-center z-50" style="display: none;">
    <div @click.away="open = false"
        class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg max-w-xl w-full p-6 mx-4 text-center relative">
        <button @click="open = false" wire:click="closeModal" aria-label="{{ t('Close modal') }}"
            class="absolute top-3 right-3 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition cursor-pointer">
            <flux:icon.x />
        </button>

        <div class="text-3xl font-bold mb-0 tracking-wider text-accent">{{ t('Active subscription') }}</div>

        <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 mb-5">
            {{ t('Thanks for subscribing!') }}
        </h2>

        <p class="text-zinc-700 dark:text-white mb-6">
            {{ t('Your subscription is active! Get ready to make the most of ClubStep benefits and take the next step with us.') }}
        </p>

        <flux:button @click="open = false;" wire:click="closeModal" variant="primary" class="w-full">
            {{ t('Close') }}
        </flux:button>
    </div>
</div>
