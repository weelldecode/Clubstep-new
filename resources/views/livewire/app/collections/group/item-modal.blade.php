@if ($showModal && $selectedItem)
    @php
        $itemImages = is_array($selectedItem->images)
            ? $selectedItem->images
            : json_decode($selectedItem->images, true);
        $imagesToShow = array_slice($itemImages ?? [], 0, 4);
        $author = $collection->user;
        $hasPurchased = $selectedItem->type === 'sites' ? $this->hasPurchasedItem($selectedItem->id) : false;
    @endphp

    <div
        x-data="{
            open: false,
            close() {
                this.open = false;
                setTimeout(() => $wire.closeModal(), 220);
            }
        }"
        x-init="$nextTick(() => open = true)"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-3 md:p-6"
        @keydown.escape.window="close()"
    >
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-black/60 backdrop-blur-sm"
            @click="close()"
        ></div>

        <div
            x-show="open"
            x-transition:enter="transform transition ease-out duration-250"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-full max-w-6xl overflow-hidden rounded-2xl border border-zinc-200/70 bg-white/95 shadow-2xl dark:border-zinc-800/80 dark:bg-zinc-950/95"
            role="dialog"
            aria-modal="true"
            @click.stop
        >
            <button
                class="absolute right-4 top-4 z-20 flex h-8 w-8 items-center justify-center rounded-full bg-zinc-100 text-zinc-600 transition hover:bg-zinc-200 hover:text-red-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
                @click="close()"
                aria-label="Fechar modal"
            >
                <flux:icon.x class="size-5" />
            </button>

            <div class="grid gap-0 md:grid-cols-[1.05fr_0.95fr]">
                <div class="border-b border-zinc-200/70 bg-zinc-100/60 p-4 dark:border-zinc-800 dark:bg-zinc-900/40 md:border-b-0 md:border-r md:p-5">
                    @if (!empty($imagesToShow))
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($imagesToShow as $img)
                                <img
                                    src="{{ asset('storage/' . $img) }}"
                                    alt="{{ $selectedItem->name }}"
                                    class="h-[180px] w-full rounded-xl object-cover md:h-[220px]"
                                />
                            @endforeach
                        </div>
                    @else
                        <img
                            src="{{ asset('images/placeholders/item-default.svg') }}"
                            alt="{{ $selectedItem->name }}"
                            class="h-[320px] w-full rounded-xl object-cover"
                        />
                    @endif
                </div>

                <div class="flex flex-col gap-5 p-5 md:p-6">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                            {{ $selectedItem->name }}
                        </h2>

                        @if (!empty($selectedItem->description))
                            <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">
                                {{ $selectedItem->description }}
                            </p>
                        @endif

                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-1 font-semibold text-zinc-700 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-300 dark:ring-zinc-700">
                                {{ t('Premium') }}
                            </span>
                            <span class="inline-flex items-center rounded-full bg-accent/10 px-2.5 py-1 font-semibold text-accent ring-1 ring-accent/20">
                                {{ t('Protected file') }}
                            </span>
                        </div>
                    </div>

                    @if ($selectedItem->type === 'sites')
                        <div class="rounded-xl border border-accent/30 bg-accent/5 p-4">
                            <h3 class="text-sm font-bold text-accent">{{ t('Single purchase') }}</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-200">
                                {{ t('This site is sold separately. Add to cart to complete the purchase.') }}
                            </p>
                            <p class="mt-3 text-xl font-black text-zinc-900 dark:text-white">
                                R$ {{ number_format((float) $selectedItem->price, 2, ',', '.') }}
                            </p>
                        </div>
                    @else
                        <div class="rounded-xl border border-accent/30 bg-accent/5 p-4">
                            <h3 class="text-sm font-bold text-accent">{{ t('Premium file') }}</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-200">
                                {{ t('This file is available to Club members. Log in or subscribe to unlock the download.') }}
                            </p>
                        </div>
                    @endif

                    @php
                        $isFavorited = $this->isFavorited($selectedItem->id);
                    @endphp

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <flux:button
                            class="w-full"
                            size="sm"
                            variant="{{ $isFavorited ? 'primary' : 'outline' }}"
                            wire:click="toggleFavorite({{ $selectedItem->id }})"
                        >
                            {{ $isFavorited ? t('Saved') : t('Save') }}
                        </flux:button>
                        <flux:button class="w-full" variant="primary" color="red" size="sm" wire:click="openReport({{ $selectedItem->id }})">
                            {{ t('Report') }}
                        </flux:button>
                    </div>

                    @auth
                        @if ($selectedItem->type === 'sites')
                            @if ($hasPurchased)
                                <a
                                    href="{{ route('items.download', $selectedItem) }}"
                                    class="inline-flex w-full items-center justify-center rounded-lg bg-accent px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90"
                                >
                                    {{ t('Download file') }}
                                </a>
                            @else
                                @php
                                    $cartHasItem = $this->hasActiveCartItem();
                                @endphp
                                @if ($cartHasItem)
                                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                                        {{ t('Your cart already has one item. Finish the purchase to add another.') }}
                                    </div>
                                    <a
                                        href="{{ route('cart.index') }}"
                                        class="inline-flex w-full items-center justify-center rounded-lg bg-accent px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90"
                                    >
                                        {{ t('Checkout') }}
                                    </a>
                                @else
                                    <flux:button
                                        wire:click="addToCart({{ $selectedItem->id }})"
                                        class="w-full"
                                        variant="primary"
                                    >
                                        {{ t('Add to cart') }}
                                    </flux:button>
                                    <a
                                        href="{{ route('cart.index') }}"
                                        class="inline-flex w-full items-center justify-center rounded-lg border border-zinc-200 px-4 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-900"
                                    >
                                        {{ t('View cart') }}
                                    </a>
                                @endif
                            @endif
                        @elseif (!auth()->user()->activeSubscription())
                            <a
                                href="/plans"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-accent px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90"
                            >
                                {{ t('Subscribe to download') }}
                            </a>
                        @else
                            <flux:button
                                wire:click="startDownload({{ $selectedItem->id }})"
                                class="w-full"
                                variant="primary"
                                icon:trailing="arrow-down-tray"
                            >
                                {{ t('Download file') }}
                            </flux:button>
                        @endif
                    @else
                        @if ($selectedItem->type === 'sites')
                            <a
                                href="/login"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-accent px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90"
                            >
                                {{ t('Log in to buy') }}
                            </a>
                        @else
                            <a
                                href="/plans"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-accent px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90"
                            >
                                {{ t('Subscribe to download') }}
                            </a>
                        @endif
                    @endauth

@if ($author)
    @php
        $authorAnimationsAllowed = $author->type === 'verified' && ($author->profile_animations_enabled ?? true);
        $authorRingStyle = $author->profileRingStyle;
        $authorAvatarUrl = null;
        if ($author->profile_image) {
            $authorAvatarUrl = asset('storage/' . $author->profile_image);
            if (
                !$authorAnimationsAllowed &&
                str_ends_with(strtolower($author->profile_image), '.gif')
            ) {
                $staticPath = preg_replace('/\\.gif$/i', '.png', $author->profile_image);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($staticPath)) {
                    $authorAvatarUrl = asset('storage/' . $staticPath);
                } else {
                    $authorAvatarUrl = null;
                }
            }
        }
    @endphp
    <style>
        @keyframes profileRingSpinSm {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .profile-ring-sm {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            isolation: isolate;
        }
        .profile-ring-sm::before {
            content: "";
            position: absolute;
            inset: -4px;
            border-radius: 9999px;
            background: var(--ring-gradient);
            animation: profileRingSpinSm var(--ring-speed) linear infinite;
            filter: blur(1px);
            opacity: 0.9;
            z-index: 0;
            pointer-events: none;
        }
        .profile-ring-sm::after {
            content: "";
            position: absolute;
            inset: -1px;
            border-radius: 9999px;
            border: 2px solid var(--ring-border);
            opacity: 0.7;
            z-index: 0;
            pointer-events: none;
        }
    </style>
    <div class="mt-1 flex items-center gap-3 rounded-xl border border-zinc-200/70 bg-white/70 p-3 dark:border-zinc-800 dark:bg-zinc-900/60">
        @if ($authorAvatarUrl)
            <div
                class="{{ $authorAnimationsAllowed ? 'profile-ring-sm' : '' }}"
                @if ($authorAnimationsAllowed)
                    style="
                        --ring-gradient: {{ $authorRingStyle?->gradient ?? 'conic-gradient(from 120deg, #22d3ee, #6366f1, #f97316, #22d3ee)' }};
                        --ring-border: {{ $authorRingStyle?->border ?? 'rgba(255,255,255,0.25)' }};
                        --ring-speed: {{ $authorRingStyle?->speed ?? '8s' }};
                    "
                @endif
            >
                <img src="{{ $authorAvatarUrl }}"
                     class="relative z-10 h-10 w-10 rounded-full object-cover"
                     alt="{{ $author->name }}">
            </div>
        @else
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-300 text-sm font-bold text-white">
                {{ $author->initials() }}
            </div>
        @endif
        <div>
            <flux:heading size="lg">{{ $author->name }}</flux:heading>
            @if ($author->type === 'verified')
                                    <flux:text class="flex items-center gap-1 text-xs">
                                        Verificado
                                        <flux:icon.badge-check class="size-4" />
                                    </flux:text>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<flux:modal wire:model="showReportModal" class="max-w-lg">
    <div class="space-y-4">
        <div>
        <flux:heading size="lg">{{ t('Report item') }}</flux:heading>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ t('Explain the reason for the report.') }}</p>
        </div>

        <div class="grid gap-3">
            <div>
                <flux:label>{{ t('Reason') }}</flux:label>
                <x-select
                    wire:model.live="reportForm.reason"
                    placeholder="Selecione o motivo"
                    :options="[
                        ['id' => 'copyright', 'name' => t('Copyright')],
                        ['id' => 'spam', 'name' => t('Spam')],
                        ['id' => 'fraud', 'name' => t('Fraud')],
                        ['id' => 'inappropriate', 'name' => t('Inappropriate content')],
                        ['id' => 'other', 'name' => t('Other')],
                    ]"
                    option-label="name"
                    option-value="id"
                />
                @error('reportForm.reason') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
            </div>

            <div>
                <flux:label>{{ t('Message (optional)') }}</flux:label>
                <flux:textarea rows="4" wire:model.live="reportForm.message" />
                @error('reportForm.message') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <flux:button variant="outline" wire:click="closeReport">{{ t('Cancel') }}</flux:button>
            <flux:button wire:click="submitReport">{{ t('Send report') }}</flux:button>
        </div>
    </div>
</flux:modal>
