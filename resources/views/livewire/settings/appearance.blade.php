<section class="w-full container mx-auto">
    @include('partials.settings-heading')

    <x-settings.layout :heading="t('Appearance')" :subheading="t('Update the appearance settings for your account')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ t('Light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ t('Dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ t('System') }}</flux:radio>
        </flux:radio.group>
    </x-settings.layout>
</section>
