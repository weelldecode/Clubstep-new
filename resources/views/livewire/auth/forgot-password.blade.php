<div class="relative min-h-screen overflow-hidden bg-zinc-800 text-zinc-100">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center blur-md brightness-75"
            style="background-image: url('https://images.unsplash.com/photo-1706546235267-f5f07efdd37e?auto=format&fit=crop&fm=jpg&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&ixlib=rb-4.1.0&q=80&w=2400');">
        </div>
        <div class="absolute inset-0 bg-zinc-900/70"></div>
        <div class="absolute -left-32 -top-32 h-72 w-72 rounded-full bg-blue-900/35 blur-2xl"></div>
        <div class="absolute right-[-6rem] top-24 h-80 w-80 rounded-full bg-blue-800/30 blur-2xl"></div>
        <div class="absolute left-1/4 bottom-[-6rem] h-80 w-[32rem] rounded-full bg-blue-950/30 blur-2xl"></div>
        <div class="absolute inset-x-0 top-0 h-1 bg-accent"></div>
    </div>

    <div class="relative flex min-h-screen flex-col">
        <div class="flex items-center justify-between px-8 pt-10 md:px-12">
            <div
                x-data="{ loading: false }"
                x-on:livewire:navigating.window="loading = true"
                x-on:livewire:navigated.window="loading = false"
                class="relative z-50 block"
            >
                <a href="{{ route('home') }}"
                   wire:navigate
                   :class="loading ? 'pointer-events-none opacity-100' : ''"
                   class="group flex flex-col items-start select-none cursor-pointer
                          transition-all duration-300">

                    <div class="flex items-center">
                        <span x-show="loading" x-cloak
                              class="inline-flex items-center justify-center">
                            <svg class="h-4 w-4 animate-spin text-zinc-500 dark:text-zinc-300"
                                 viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z"></path>
                            </svg>
                        </span>

                        <span class="hidden md:inline text-2xl md:text-4xl font-black tracking-tight
                                     text-zinc-200 transition-colors duration-300
                                     group-hover:text-white">
                            Club
                        </span>

                        <span class="relative font-black tracking-tight text-accent
                                     text-3xl md:text-4xl
                                     transition-all duration-300
                                     group-hover:tracking-wide group-hover:scale-[1.03]">
                            Step
                        </span>
                    </div>

                    <span class="mt-0.5 text-[11px] md:text-xs
                                 text-zinc-400 tracking-wide">
                        {{ t('Sites • Collections • Arts') }}
                    </span>
                </a>
            </div>
            <div class="hidden text-xs uppercase tracking-[0.35em] text-blue-100/70 md:block">
                {{ t('Your flow in motion') }}
            </div>
        </div>

        <div class="flex flex-1 items-center justify-center px-8 py-12 md:px-12">
            <div class="flex w-full max-w-md flex-col gap-6 rounded-3xl border border-zinc-600/60 bg-zinc-900/70 p-8 shadow-[0_35px_90px_-60px_rgba(0,0,0,0.85)] ring-1 ring-white/10 backdrop-blur-md">
                <x-auth-header :title="t('Forgot my PIN')" :description="t('Enter your email to receive the PIN reset link')" />

                <x-auth-session-status class="text-center" :status="session('status')" />

                <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
                    <flux:input
                        wire:model="email"
                        :label="t('Email address')"
                        type="email"
                        required
                        autofocus
                        placeholder="email@example.com"
                    />

                    <flux:button variant="primary" type="submit" class="w-full">{{ t('Send PIN reset link') }}</flux:button>
                </form>

                <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
                    <span>{{ t('Or, return to') }}</span>
                    <flux:link :href="route('login')" wire:navigate>{{ t('log in') }}</flux:link>
                </div>
            </div>
        </div>
    </div>
</div>
