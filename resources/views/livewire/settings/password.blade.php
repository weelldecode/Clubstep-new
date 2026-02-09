<section class="w-full container mx-auto">
    @include('partials.settings-heading')

    <x-settings.layout :heading="t('Update PIN')" :subheading="t('Keep your PIN secure to access your account')">
        <form wire:submit="updatePin" class="mt-6 space-y-6">
            <flux:input
                wire:model="current_pin"
                :label="t('Current PIN')"
                type="password"
                required
                autocomplete="off"
            />
            <flux:input
                wire:model="pin"
                :label="t('New PIN')"
                type="password"
                required
                autocomplete="off"
            />
            <flux:input
                wire:model="pin_confirmation"
                :label="t('Confirm PIN')"
                type="password"
                required
                autocomplete="off"
            />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ t('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="pin-updated">
                    {{ t('PIN updated.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
