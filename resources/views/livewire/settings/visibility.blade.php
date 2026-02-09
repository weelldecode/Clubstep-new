<section class="w-full container mx-auto">
    @include('partials.settings-heading')

    <x-settings.layout :heading="t('Visibility')" :subheading="t('Change the visibility of your collections and information.')">
        <div class="my-6 space-y-5">
            <div class="rounded-2xl border border-zinc-200/70 bg-white/80 p-5 shadow-[0_20px_50px_-45px_rgba(0,0,0,0.45)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/75">
                <flux:heading>{{ t('Visibility settings') }}</flux:heading>
                <flux:subheading class="mt-1">
                    {{ t('Define exactly which information from your profile is public.') }}
                </flux:subheading>

                <div class="mt-5 space-y-4">
                    <div class="rounded-xl border border-zinc-200/70 bg-zinc-50/70 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                        <flux:switch wire:model.live="is_private"
                                     label="{{ t('Private profile') }}"
                                     description="{{ t('Only approved users can see your profile, collections, and activities.') }}" />
                    </div>

                    <div class="rounded-xl border border-zinc-200/70 bg-zinc-50/70 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                        <flux:switch wire:model.live="hide_collections"
                                     label="{{ t('Hide collections') }}"
                                     description="{{ t('Your collections will not be visible to other users.') }}" />
                    </div>

                    <div class="rounded-xl border border-zinc-200/70 bg-zinc-50/70 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                        <flux:switch wire:model.live="hide_followers"
                                     label="{{ t('Hide followers') }}"
                                     description="{{ t('No one will be able to see who follows you.') }}" />
                    </div>

                    <div class="rounded-xl border border-zinc-200/70 bg-zinc-50/70 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                        <flux:switch wire:model.live="hide_following"
                                     label="{{ t('Hide following') }}"
                                     description="{{ t('No one will be able to see the list of users you follow.') }}" />
                    </div>
                </div>
            </div>
        </div>
 
    </x-settings.layout>


</section>
