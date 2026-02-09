<div class="relative min-h-screen overflow-hidden bg-zinc-800 text-zinc-100" x-data="{
    step: @entangle('step'),
    pin: @entangle('pin').defer,
    pin_confirmation: @entangle('pin_confirmation').defer
}">
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

                    {{-- Linha da logo --}}
                    <div class="flex items-center">
                        {{-- Spinner --}}
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

                        {{-- Club --}}
                        <span class="hidden md:inline text-2xl md:text-4xl font-black tracking-tight
                                     text-zinc-200 transition-colors duration-300
                                     group-hover:text-white">
                            Club
                        </span>

                        {{-- Step --}}
                        <span class="relative font-black tracking-tight text-accent
                                     text-3xl md:text-4xl
                                     transition-all duration-300
                                     group-hover:tracking-wide group-hover:scale-[1.03]">
                            Step
                        </span>
                    </div>

                    {{-- Subtítulo --}}
                    <span class="mt-0.5 text-[11px] md:text-xs
                                 text-zinc-400 tracking-wide">
                        {{ t('Sites • Collections • Arts') }}
                    </span>
                </a>
            </div>
            <div class="hidden text-xs uppercase tracking-[0.35em] text-blue-100/70 md:block">
                {{ t('Create account') }}
            </div>
        </div>

        <div class="flex flex-1 items-center justify-center px-8 py-12 md:px-12">
            <div class="flex w-full max-w-md flex-col gap-6 rounded-3xl border border-zinc-700/70 bg-zinc-900/60 p-8 shadow-[0_30px_70px_-50px_rgba(0,0,0,0.75)] backdrop-blur-sm">
                <x-auth-header 
                    :title="
                        $step === 1 ? t('Create an account') : 
                        ($step === 2 ? t('Set your PIN') : t('Confirm your PIN'))
                    " 
                    :description="
                        $step === 1 ? t('Enter your details below to create your account') : 
                        ($step === 2 ? t('Enter a 6-digit PIN to secure your account') : 
                        t('Confirm your 6-digit PIN to proceed'))
                    " 
                />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit.prevent="submitStep" novalidate class="flex flex-col gap-6">
        <div x-show="step === 1" class="flex flex-col gap-5">
            <!-- Step 1: Name and Email -->


            <flux:field>
                <flux:label>{{ t('Username') }}</flux:label>

                <flux:input wire:model.defer="name" type="text" required autofocus
                    autocomplete="name" :placeholder="t('Full name')" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ t('Email') }}</flux:label>

            <flux:input wire:model.defer="email" type="email" required
                autocomplete="email" placeholder="email@example.com" />
                <flux:error name="email" />
            </flux:field> 

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ t('Next') }}
                </flux:button>
            </div>
        </div>


        <div x-show="step === 2" class="flex flex-col gap-5">
            <div class="rounded-2xl border border-zinc-700/70 bg-zinc-800/70 p-4">
                <div class="mb-3 flex items-center justify-between text-xs uppercase tracking-[0.2em] text-zinc-400">
                    <span>{{ t('Enter PIN') }}</span>
                    <span class="text-zinc-500 dark:text-zinc-100">{{ t('6 digits') }}</span>
                </div>
                <div class="relative" x-data="{ pin: @entangle('pin') }"> 

                    <input x-ref="pinInput" type="text" maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                        class="absolute left-0 top-0 h-12 w-full opacity-0" x-model="pin" wire:model="pin" autofocus />

                    <div tabindex="0" x-on:click="$refs.pinInput.focus()"
                        class="flex justify-between gap-2 text-zinc-100"
                        style="user-select: none;">
                        <template x-for="(digit, index) in 6" :key="index">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl border-2 text-xl transition"
                                :class="{
                                    'border-accent bg-accent/15 text-accent': pin.length === index || (pin.length === 6 && index === 5),
                                    'border-zinc-700 bg-zinc-900': !(pin.length === index || (pin.length === 6 && index === 5))
                                }">
                                <span x-text="pin[index] || ''"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="flex justify-between">
                <flux:button type="button" variant="danger" wire:click="previousStep">
                    {{ t('Back') }}
                </flux:button>
 
            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ t('Next') }}
                </flux:button>
            </div>
            </div>
        </div>

        
        <div x-show="step === 3" class="flex flex-col gap-5">
            <div class="rounded-2xl border border-zinc-700/70 bg-zinc-800/70 p-4">
                <div class="mb-3 flex items-center justify-between text-xs uppercase tracking-[0.2em] text-zinc-400">
                    <span>{{ t('Confirm PIN') }}</span>
                    <span class="text-zinc-500 dark:text-zinc-100">{{ t('6 digits') }}</span>
                </div>
                <div class="relative" x-data="{ pin_confirmation: @entangle('pin_confirmation') }"> 

                    <input x-ref="pinCInput" type="text" maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                        class="absolute left-0 top-0 h-12 w-full opacity-0" x-model="pin_confirmation" wire:model="pin_confirmation" autofocus />

                    <div tabindex="0" x-on:click="$refs.pinCInput.focus()"
                        class="flex justify-between gap-2 text-zinc-100"
                        style="user-select: none;">
                        <template x-for="(digit, index) in 6" :key="index">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl border-2 text-xl transition"
                                :class="{
                                    'border-accent bg-accent/15 text-accent': pin_confirmation.length === index || (pin_confirmation.length === 6 && index === 5),
                                    'border-zinc-700 bg-zinc-900': !(pin_confirmation.length === index || (pin_confirmation.length === 6 && index === 5))
                                }">
                                <span x-text="pin_confirmation[index] || ''"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="flex justify-between">
                <flux:button type="button" variant="danger" wire:click="previousStep">
                    {{ t('Back') }}
                </flux:button>

                <flux:button type="submit" variant="primary">
                    {{ t('Create account') }}
                </flux:button>
            </div>
        </div>
    </form>

                @if ($step === 1)
                    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
                        <span>{{ t('Already have an account?') }}</span>
                        <flux:link :href="route('login')" wire:navigate>{{ t('Log in') }}</flux:link>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
