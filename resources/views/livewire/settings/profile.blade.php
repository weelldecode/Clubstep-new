<section class="w-full container mx-auto">
    @include('partials.settings-heading')

    <x-settings.layout :heading="t('Profile')" :subheading="t('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-5">
            <div class="rounded-2xl border border-zinc-200/70 bg-white/80 p-5 shadow-[0_20px_50px_-45px_rgba(0,0,0,0.45)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/75">
                <flux:heading>{{ t('Account Information') }}</flux:heading>
                <flux:subheading class="mt-1">{{ t('Update your public name and login email.') }}</flux:subheading>

                <div class="mt-4 space-y-4">
                    <flux:input wire:model="name" :label="t('Name')" type="text" required autofocus autocomplete="name" />
                    <flux:input wire:model="email" :label="t('Email')" type="email" required autocomplete="email" />
                </div>
            </div>

            <div class="rounded-2xl border border-zinc-200/70 bg-white/80 p-5 shadow-[0_20px_50px_-45px_rgba(0,0,0,0.45)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/75">
                <flux:heading>{{ t('Language') }}</flux:heading>
                <flux:subheading class="mt-1">{{ t('Choose the language for your account.') }}</flux:subheading>

                <div class="mt-4">
                    <flux:field>
                        <flux:label>{{ t('Language') }}</flux:label>
                        <flux:select wire:model="locale">
                            @foreach ($localeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>
            </div>

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4">
                    <flux:text>
                        {{ t('Your email address is unverified.') }}
                        <flux:link class="text-sm cursor-pointer font-semibold" wire:click.prevent="resendVerificationNotification">
                            {{ t('Click here to re-send the verification email.') }}
                        </flux:link>
                    </flux:text>

                    @if (session('status') === 'verification-link-sent')
                        <flux:text class="mt-2 font-medium !text-emerald-600 !dark:text-emerald-400">
                            {{ t('A new verification link has been sent to your email address.') }}
                        </flux:text>
                    @endif
                </div>
            @endif

            <div class="rounded-2xl border border-zinc-200/70 bg-white/80 p-5 shadow-[0_20px_50px_-45px_rgba(0,0,0,0.45)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/75">
                <flux:heading>{{ t('Profile Picture') }}</flux:heading>
                <flux:subheading class="mt-1">{{ t('Use a clear image for better recognition.') }}</flux:subheading>

                <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <img
                        src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('images/default-avatar.png') }}"
                        class="h-20 w-20 rounded-full object-cover ring-2 ring-zinc-200 dark:ring-zinc-700"
                        alt="{{ t('Current avatar') }}"
                    >
                    <div class="w-full">
                        <flux:input type="file" wire:model="photo" />
                        @error('photo')
                            <span class="mt-1 block text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if ($photo)
                    <div class="mt-4 flex items-center gap-3 rounded-xl border border-zinc-200/70 bg-zinc-50/70 p-3 dark:border-zinc-700 dark:bg-zinc-800/60">
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ t('Preview') }}:</span>
                        <img src="{{ $photo->temporaryUrl() }}" class="h-14 w-14 rounded-full object-cover" alt="{{ t('New photo preview') }}">
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ t('Save') }}</flux:button>
                <x-action-message class="me-3" on="profile-updated">
                    {{ t('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <div class="mt-6 rounded-2xl border border-zinc-200/70 bg-white/80 p-5 shadow-[0_20px_50px_-45px_rgba(0,0,0,0.45)] backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/75">
            <livewire:settings.delete-user-form />
        </div>
    </x-settings.layout>
</section>
