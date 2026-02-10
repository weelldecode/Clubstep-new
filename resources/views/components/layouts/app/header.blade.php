<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-zinc-50 dark:bg-zinc-800">
    <x-site-loader />

    <flux:header
        class="sticky top-0 z-50 bg-white/80 dark:bg-zinc-950/80 border-b border-b-zinc-200/60 dark:border-zinc-800/60 backdrop-blur supports-[backdrop-filter]:bg-white/60 supports-[backdrop-filter]:dark:bg-zinc-950/60 flex flex-col items-start w-full shadow-[0_8px_30px_-24px_rgba(0,0,0,0.35)]">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <div class="w-full">
            <div class="h-20 px-6 md:px-10 flex items-center justify-between w-full gap-8">
                <div class="w-full flex items-center justify-between gap-6">
                    <div
                        x-data="{ loading: false }"
                        x-on:livewire:navigating.window="loading = true"
                        x-on:livewire:navigated.window="loading = false"
                        class="m-auto md:m-0"
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
                                             text-zinc-700 dark:text-zinc-200 transition-colors duration-300
                                             group-hover:text-zinc-900 dark:group-hover:text-white">
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
                                         text-zinc-500 dark:text-zinc-400 tracking-wide">
                                {{ t('Sites • Collections • Arts') }}
                            </span>
                        </a>
                    </div>

                    <div class="hidden lg:block w-full max-w-2xl">
                        <livewire:components.search-collections />
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                                @if (!auth()->user())
                                    <flux:button href="{{ route('plans') }}" variant="primary">
                                        {{ t('Get Unlimited Downloads') }}
                                    </flux:button>
                                @endif
                            </flux:navbar>
                            @auth
                                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'verified')
                                    <flux:button href="{{ route('studio.dashboard') }}" variant="primary">
                                        <flux:icon name="plus" variant="mini" /> {{ t('Publish') }}
                                    </flux:button>
                                @endif
                                <flux:modal.trigger name="cart-drawer">
                                    <div class="relative">
                                        <flux:button variant="outline">
                                            <flux:icon name="shopping-bag" variant="mini" /> {{ t('Cart') }}
                                        </flux:button>
                                        <livewire:components.cart-badge />
                                    </div>
                                </flux:modal.trigger>
                                <a href="{{ route('wishlist.index') }}"
                                   class="relative inline-flex h-10 w-10 items-center justify-center transition"
                                   title="{{ t('Wishlist') }}">
                                    <flux:icon name="heart" class="size-5" variant="solid" />
                                    <span class="absolute -top-1.5 -right-1.5">
                                        <livewire:components.wishlist-badge />
                                    </span>
                                </a>
                                <livewire:components.notifications-dropdown />
                                <!-- Desktop User Menu -->
                                <flux:dropdown position="top" align="end">
                                    @php
                                        $avatar = auth()->user()->avatar();
                                    @endphp

                                    @if ($avatar['type'] === 'image')
                                        <flux:profile avatar="{{ $avatar['value'] }}" name="{{ auth()->user()->name }}" />
                                    @else
                                        <flux:profile class="cursor-pointer" :initials="$avatar['value']" />
                                    @endif


                                    <flux:menu>
                                        <flux:menu.group :heading="t('Account')">
                                            <flux:menu.item :href="route('profile.user', auth()->user()->slug)">{{ t('My Account') }}
                                            </flux:menu.item>
                                            <flux:menu.item :href="route('settings.profile')">{{ t('Settings') }}</flux:menu.item>
                                            <flux:menu.item :href="route('download')">{{ t('Downloads') }}</flux:menu.item>
                                            <flux:menu.item :href="route('wishlist.index')">{{ t('Wishlist') }}</flux:menu.item>
                                        </flux:menu.group>
                                        <flux:menu.group :heading="t('Billing')">
                                            <flux:menu.item :href="route('billing')">{{ t('Subscription') }}</flux:menu.item>
                                        </flux:menu.group>
                                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                                            @csrf
                                            <flux:menu.item as="button" type="submit"
                                                icon="arrow-right-start-on-rectangle" class="w-full">
                                                {{ t('Log Out') }}
                                            </flux:menu.item>
                                        </form>
                                    </flux:menu>

                                </flux:dropdown>
                            @else
                                <a href="{{ route('login') }}"
                                    class="w-[10rem] rounded-lg font-semibold text-base flex flex-col text-zinc-700 dark:text-zinc-50 hover:bg-zinc-100 dark:hover:bg-zinc-900 px-4 py-2 transition">
                                    {{ t('Hello, sign in') }}
                                    <span class="font-semibold text-xs text-zinc-500 dark:text-zinc-300 -mt-1">{{ t('Accounts and Collections') }}</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="relative w-full px-6 md:px-10 bg-transparent border-t border-zinc-200/60 dark:border-zinc-800/60">
            <div class="flex items-center justify-between w-full">
                <flux:navbar class="max-lg:hidden w-full">
                    <flux:navbar.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
                        {{ t('Home') }}
                    </flux:navbar.item>
                    <flux:navbar.item :href="route('collection.index')"
                        :current="request()->routeIs('collection.*')" wire:navigate>
                        {{ t('Collections') }}
                    </flux:navbar.item>
                    @foreach ($globalCategories as $category)
                        <flux:navbar.item :href="route('collection.category', ['category' => $category->slug])"
                            :current="request()->routeIs('collection.category') && optional(request()->route('category'))->slug === $category->slug"
                            wire:navigate>
                            {{ $category->name }}
                        </flux:navbar.item>
                    @endforeach

                </flux:navbar>
                @auth
                    @if (!auth()->user()->activeSubscription)
                        <flux:button href="{{ route('plans') }}" variant="primary">
                            {{ t('Get Unlimited Downloads') }}
                        </flux:button>
                    @endif
                @endauth
            </div>
        </nav>
    </flux:header>

    <flux:modal name="cart-drawer" variant="flyout" position="right" class="md:[:where(&)]:min-w-[20rem] md:[:where(&)]:max-w-[22rem]">
        <livewire:components.cart-drawer />
    </flux:modal>

    <!-- Mobile Menu -->
    <flux:sidebar stashable sticky
        class="lg:hidden border-e border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('home') }}" class="  w-62 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="t('Platform')">
                <flux:navlist.item icon="layout-grid" :href="route('home')" :current="request()->routeIs('home')"
                    wire:navigate>
                    {{ t('Home') }}
                </flux:navlist.item>
                @auth
                    <flux:navlist.item icon="shopping-bag" :href="route('cart.index')" :current="request()->routeIs('cart.index')"
                        wire:navigate>
                        {{ t('Cart') }}
                    </flux:navlist.item>
                @endauth
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit"
                target="_blank">
                {{ t('Repository') }}
            </flux:navlist.item>

            <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire"
                target="_blank">
                {{ t('Documentation') }}
            </flux:navlist.item>
        </flux:navlist>
    </flux:sidebar>
 
        {{ $slot }}

        <footer
            class="relative w-full bg-white/90 dark:bg-zinc-950/90 border-t border-zinc-200/70 dark:border-zinc-800/70 mt-24 backdrop-blur supports-[backdrop-filter]:bg-white/70 supports-[backdrop-filter]:dark:bg-zinc-950/70">
            <div class="absolute inset-x-0 top-0 h-1 bg-accent/80"></div>

            <div class="container mx-auto px-6 pt-14 pb-8">

                {{-- GRID PRINCIPAL --}}
                <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-12">

                    {{-- LOGO + DESCRIÇÃO --}}
                    <div class="lg:col-span-4 space-y-5">

                        <div
                            x-data="{ loading: false }"
                            x-on:livewire:navigating.window="loading = true"
                            x-on:livewire:navigated.window="loading = false"
                        >
                            <a href="{{ route('home') }}"
                               wire:navigate
                               :class="loading ? 'pointer-events-none opacity-80' : ''"
                               class="group inline-flex flex-col select-none transition-all duration-300">

                                <div class="flex items-center gap-1">
                                    {{-- Spinner --}}
                                    <span x-show="loading" x-cloak>
                                        <svg class="h-4 w-4 animate-spin text-zinc-400"
                                             viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                  d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z"></path>
                                        </svg>
                                    </span>

                                    <span class="text-3xl font-black tracking-tight text-zinc-600 dark:text-zinc-300
                                                 group-hover:text-zinc-800 dark:group-hover:text-white transition">
                                        Club
                                    </span>

                                    <span class="text-3xl font-black tracking-tight text-accent">
                                        Step
                                    </span>
                                </div>

                                <span class="mt-1 text-xs tracking-wide text-zinc-400">
                                    {{ t('Sites • Collections • Arts') }}
                                </span>
                            </a>
                        </div>

                        <p class="text-sm text-zinc-500 dark:text-zinc-300 max-w-sm leading-relaxed">
                            {{ t('Professional collections, mockups, and ready-made sites to accelerate your projects — simple, fast, and affordable.') }}
                        </p>
                    </div>

                    {{-- COLUNA 1 --}}
                    <nav class="lg:col-span-2 space-y-3">
                        <p class="text-xs font-bold tracking-widest text-zinc-400 uppercase">
                            {{ t('Navigation') }}
                        </p>

                        <a href="{{ route('home') }}"
                           class="block text-sm text-zinc-600 dark:text-zinc-300 hover:text-accent transition">
                            {{ t('Home') }}
                        </a>

                        <a href="{{ route('collection.index') ?? '#' }}"
                           class="block text-sm text-zinc-600 dark:text-zinc-300 hover:text-accent transition">
                            {{ t('Collections') }}
                        </a>
                    </nav>

                    {{-- COLUNA 2 --}}
                    <nav class="lg:col-span-2 space-y-3">
                        <p class="text-xs font-bold tracking-widest text-zinc-400 uppercase">
                            {{ t('Discover') }}
                        </p>

                        <a href="#"
                           class="block text-sm text-zinc-600 dark:text-zinc-300 hover:text-accent transition">
                            Instagram
                        </a>

                        <a href="#"
                           class="block text-sm text-zinc-600 dark:text-zinc-300 hover:text-accent transition">
                            {{ t('WhatsApp') }}
                        </a>
                    </nav>

                    {{-- COLUNA 3 --}}
                    <nav class="lg:col-span-2 space-y-3">
                        <p class="text-xs font-bold tracking-widest text-zinc-400 uppercase">
                            {{ t('Legal') }}
                        </p>

                        <a href="#"
                           class="block text-sm text-zinc-600 dark:text-zinc-300 hover:text-accent transition">
                            {{ t('Terms of Use') }}
                        </a>

                        <a href="#"
                           class="block text-sm text-zinc-600 dark:text-zinc-300 hover:text-accent transition">
                            {{ t('Privacy Policy') }}
                        </a>
                    </nav>

                    {{-- COLUNA 4 --}}
                    <nav class="lg:col-span-2 space-y-3">
                        <p class="text-xs font-bold tracking-widest text-zinc-400 uppercase">
                            {{ t('Contact') }}
                        </p>

                        <a href="mailto:contato@clubstep.com"
                           class="block text-sm text-zinc-600 dark:text-zinc-300 hover:text-accent transition">
                            {{ t('Email') }}
                        </a>

                        <a href="#"
                           class="block text-sm text-zinc-600 dark:text-zinc-300 hover:text-accent transition">
                            {{ t('Support') }}
                        </a>
                    </nav>
                </div>

                {{-- DIVISOR --}}
                <div class="mt-14 pt-6 border-t border-zinc-200 dark:border-zinc-800
                            flex flex-col md:flex-row items-center justify-between gap-4">

                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        © 2026 <span class="font-semibold text-zinc-700 dark:text-zinc-200">ClubStep</span>.
                        {{ t('All rights reserved.') }}
                    </p>

                    <div class="flex items-center gap-4 text-xs text-zinc-400">
                        <span>{{ t('Made with ❤️ by DevStep Studio.') }}</span>
                    </div>
                </div>
            </div>
        </footer>
        <livewire:components.subscription-modal />
        <wireui:scripts />
        @fluxScripts
        <x-toast />
</body>

</html>
