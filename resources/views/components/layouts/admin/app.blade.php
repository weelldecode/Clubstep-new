<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite(['resources/css/app.css','resources/js/app.js'])
    @fluxAppearance
    @wireUiScripts
</head>
<body class="min-h-screen bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100">
<x-site-loader />

@php
    $routeName = request()->route()?->getName() ?? "";
    $pageTitle = match (true) {
        str_starts_with($routeName, "admin.dashboard") => t("Dashboard"),
        str_starts_with($routeName, "admin.users.index") => t("Users"),
        str_starts_with($routeName, "admin.collections.items") => t("Collection items"),
        str_starts_with($routeName, "admin.collections.index") => t("Collections"),
        str_starts_with($routeName, "admin.categories.index") => t("Categories"),
        str_starts_with($routeName, "admin.tags.index") => t("Tags"),
        str_starts_with($routeName, "admin.plans.index") => t("Plans"),
        str_starts_with($routeName, "admin.subscriptions.index") => t("Subscriptions"),
        str_starts_with($routeName, "admin.payments.index") => t("Payments"),
        str_starts_with($routeName, "admin.orders.index") => t("Orders"),
        str_starts_with($routeName, "admin.reports.index") => t("Reports"),
        str_starts_with($routeName, "admin.email-templates.index") => t("Email templates"),
        str_starts_with($routeName, "admin.translations.index") => t("Translations"),
        default => t("Admin"),
    };
@endphp

<div class="flex min-h-screen">
    <aside class="w-72 shrink-0 border-r border-zinc-200/80 bg-white/90 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/80">
        <div class="flex h-full flex-col">
            <div class="border-b border-zinc-200/70 px-5 py-5 dark:border-zinc-800">
                <div class="text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">{{ t('Panel') }}</div>
                <div class="mt-1 text-xl font-black tracking-tight text-zinc-900 dark:text-zinc-100">ClubStep Admin</div>
            </div>

            <nav class="space-y-5 px-3 py-4">
                <div class="px-2">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-400 dark:text-zinc-400">{{ t('General') }}</div>
                    <div class="mt-2 space-y-1">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('admin.dashboard') ? 'bg-accent/10 text-accent ring-1 ring-accent/20' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                >
                    <flux:icon name="layout-grid" class="size-4" />
                    {{ t('Dashboard') }}
                </a>
                <a
                    href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('admin.users.*') ? 'bg-accent/10 text-accent ring-1 ring-accent/20' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                >
                    <flux:icon name="users" class="size-4" />
                    {{ t('Users') }}
                </a>
                    </div>
                </div>

                <div class="px-2">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-400 dark:text-zinc-400">{{ t('Content') }}</div>
                    <div class="mt-2 space-y-1">
                        <a
                            href="{{ route('admin.collections.index') }}"
                            class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('admin.collections.*') ? 'bg-accent/10 text-accent ring-1 ring-accent/20' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            <flux:icon name="folder-open" class="size-4" />
                            {{ t('Collections') }}
                        </a>

                        <a
                            href="{{ route('admin.categories.index') }}"
                            class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('admin.categories.*') ? 'bg-accent/10 text-accent ring-1 ring-accent/20' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            <flux:icon name="squares-2x2" class="size-4" />
                            {{ t('Categories') }}
                        </a>

                        <a
                            href="{{ route('admin.tags.index') }}"
                            class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('admin.tags.*') ? 'bg-accent/10 text-accent ring-1 ring-accent/20' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            <flux:icon name="tag" class="size-4" />
                            {{ t('Tags') }}
                        </a>
                    </div>
                </div>

                <div class="px-2">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-400 dark:text-zinc-400">{{ t('Billing') }}</div>
                    <div class="mt-2 space-y-1">
                        <a
                            href="{{ route('admin.plans.index') }}"
                            class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('admin.plans.*') ? 'bg-accent/10 text-accent ring-1 ring-accent/20' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            <flux:icon name="credit-card" class="size-4" />
                            {{ t('Plans') }}
                        </a>

                        <a
                            href="{{ route('admin.subscriptions.index') }}"
                            class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('admin.subscriptions.*') ? 'bg-accent/10 text-accent ring-1 ring-accent/20' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            <flux:icon name="calendar-days" class="size-4" />
                            {{ t('Subscriptions') }}
                        </a>

                        <a
                            href="{{ route('admin.payments.index') }}"
                            class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('admin.payments.*') ? 'bg-accent/10 text-accent ring-1 ring-accent/20' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            <flux:icon name="banknotes" class="size-4" />
                            {{ t('Payments') }}
                        </a>

                        <a
                            href="{{ route('admin.orders.index') }}"
                            class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('admin.orders.*') ? 'bg-accent/10 text-accent ring-1 ring-accent/20' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            <flux:icon name="shopping-bag" class="size-4" />
                            {{ t('Orders') }}
                        </a>
                    </div>
                </div>

                <div class="px-2">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-400 dark:text-zinc-400">{{ t('Moderation') }}</div>
                    <div class="mt-2 space-y-1">
                        <a
                            href="{{ route('admin.reports.index') }}"
                            class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('admin.reports.*') ? 'bg-accent/10 text-accent ring-1 ring-accent/20' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            <flux:icon name="flag" class="size-4" />
                            {{ t('Reports') }}
                        </a>
                    </div>
                </div>

                <div class="px-2">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-400 dark:text-zinc-400">{{ t('Communication') }}</div>
                    <div class="mt-2 space-y-1">
                        <a
                            href="{{ route('admin.email-templates.index') }}"
                            class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('admin.email-templates.*') ? 'bg-accent/10 text-accent ring-1 ring-accent/20' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            <flux:icon name="envelope" class="size-4" />
                            {{ t('Email templates') }}
                        </a>

                        <a
                            href="{{ route('admin.translations.index') }}"
                            class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('admin.translations.*') ? 'bg-accent/10 text-accent ring-1 ring-accent/20' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            <flux:icon name="language" class="size-4" />
                            {{ t('Translations') }}
                        </a>
                    </div>
                </div>
            </nav>

            <div class="mt-auto border-t border-zinc-200/70 p-4 dark:border-zinc-800">
                <a
                    href="{{ route('home') }}"
                    class="mb-3 inline-flex w-full items-center justify-center rounded-lg border border-zinc-200 px-3 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                >
                    {{ t('Back to site') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-lg bg-zinc-900 px-3 py-2 text-sm font-semibold text-white transition hover:opacity-90 dark:bg-zinc-100 dark:text-zinc-900"
                    >
                        {{ t('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="min-w-0 flex-1">
        <header class="sticky top-0 z-30 border-b border-zinc-200/80 bg-white/85 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/80">
            <div class="flex h-16 items-center justify-between px-6">
                <div>
                    <p class="text-base font-bold tracking-tight text-zinc-800 dark:text-zinc-100">{{ $pageTitle }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Manage platform content and operations') }}</p>
                </div>

                <div class="flex items-center gap-3 rounded-xl border border-zinc-200/70 bg-white/70 px-3 py-2 dark:border-zinc-800 dark:bg-zinc-900/70">
                    <flux:avatar>
                        {{ auth()->user()->initials() }}
                    </flux:avatar>
                    <div class="min-w-0">
                        <div class="truncate text-sm font-semibold text-zinc-800 dark:text-zinc-100">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ t('Administrator') }}</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="p-6 md:p-8">
            <div class="mx-auto max-w-7xl">
                {{ $slot }}
            </div>
        </div>
    </main>
</div>
<wireui:scripts />
@livewireScripts
@fluxScripts
<x-toast />
</body>
</html>
