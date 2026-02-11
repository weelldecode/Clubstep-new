@php
    $profileImage = $user->profile_image
        ? asset('storage/' . $user->profile_image)
        : asset('images/placeholders/profile-default.svg');
    $animationsAllowed = $user->type === 'verified' && ($user->profile_animations_enabled ?? true);
    $showProfileRing = $animationsAllowed;
    $ringStyle = $user->profileRingStyle;
    $avatarPath = $user->profile_image;
    $avatarUrl = $avatarPath ? asset('storage/' . $avatarPath) : null;
    $avatarCachePath = $avatarPath;
    if (!$animationsAllowed && $avatarPath && str_ends_with(strtolower($avatarPath), '.gif')) {
        $staticAvatarPath = preg_replace('/\\.gif$/i', '.png', $avatarPath);
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($staticAvatarPath)) {
            $avatarUrl = asset('storage/' . $staticAvatarPath);
            $avatarCachePath = $staticAvatarPath;
        } else {
            $avatarUrl = asset('images/placeholders/profile-default.svg');
            $avatarCachePath = null;
        }
    }
    $avatarVersion = $avatarCachePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($avatarCachePath)
        ? \Illuminate\Support\Facades\Storage::disk('public')->lastModified($avatarCachePath)
        : time();
    $avatarSrc = $avatarUrl ? $avatarUrl . '?v=' . $avatarVersion : null;
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "ProfilePage",
        "mainEntity" => [
            "@type" => "Person",
            "name" => $user->name,
            "image" => $profileImage,
            "url" => url()->current(),
        ],
    ];
@endphp

@push('seo')
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@php
    $bannerPath = $user->profile_banner;
    $bannerUrl = $user->bannerUrl()
        ?? 'https://mangadex.org/img/group-banner.png';
    $bannerCachePath = $bannerPath;
    if (!$animationsAllowed && $bannerPath && str_ends_with(strtolower($bannerPath), '.gif')) {
        $bannerCachePath = preg_replace('/\\.gif$/i', '.png', $bannerPath);
    }
    $bannerVersion = $bannerCachePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($bannerCachePath)
        ? \Illuminate\Support\Facades\Storage::disk('public')->lastModified($bannerCachePath)
        : time();
    $bannerSrc = $bannerUrl . '?v=' . $bannerVersion;
@endphp

<div>
    @if ($showProfileRing)
        <style>
            @keyframes profileRingSpin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            @keyframes profileRingPulse {
                0%, 100% { opacity: 0.65; }
                50% { opacity: 1; }
            }
            .profile-ring::before {
                content: "";
                position: absolute;
                inset: -6px;
                border-radius: 9999px;
                background: var(--ring-gradient);
                animation: profileRingSpin var(--ring-speed) linear infinite;
                filter: blur(1.5px);
                box-shadow: 0 0 18px var(--ring-glow);
                z-index: 0;
                pointer-events: none;
            }
            .profile-ring::after {
                content: "";
                position: absolute;
                inset: -2px;
                border-radius: 9999px;
                border: 2px solid var(--ring-border);
                animation: profileRingPulse 2.4s ease-in-out infinite;
                z-index: 0;
                pointer-events: none;
            }
            .profile-ring {
                isolation: isolate;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        </style>
    @endif
    {{-- Banner --}}
    <div id="bannerComponent" class="relative top-0 w-full h-[22rem] md:h-[24rem] shadow overflow-hidden">

        {{-- Banner atual --}}
        <img id="bannerDisplay" wire:ignore
            src="{{ $bannerSrc }}"
            class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>

        @auth
            @if (auth()->id() === $user->id)
                {{-- Input de arquivo escondido --}}
                <input type="file" id="bannerInput" class="hidden" accept="image/*">

                {{-- Botão hover para selecionar --}}
                <button type="button" onclick="document.getElementById('bannerInput').click()"
                    class="absolute top-0 right-0 px-4 py-2 bg-black/55 w-full h-full font-bold text-white rounded-lg opacity-0 hover:opacity-100 transition cursor-pointer">
                    {{ t('Change banner') }}
                </button>

                {{-- Canvas para crop, inicial invisível --}}

                {{-- Canvas real para crop --}}
                <canvas id="bannerCanvas" width="1865" height="320"
                    class="absolute top-0 left-0 w-full h-full hidden cursor-grab"></canvas>

                {{-- Canvas overlay só para grade --}}
                <canvas id="gridOverlay" width="1865" height="320"
                    class="absolute top-0 left-0 w-full h-full pointer-events-none hidden"></canvas>
                <div class="flex items-center gap-5">

                    {{-- Botão salvar crop --}}
                    <button id="saveBannerBtn" onclick="saveBannerCrop()"
                        class="absolute bottom-4 right-4 px-5 z-50 py-2 bg-accent hover:bg-accent-content font-bold text-white rounded-lg hidden">
                        {{ t('Save banner') }}
                    </button>
                    {{-- Botão resetar posição --}}
                    <button id="resetBannerBtn" onclick="resetBannerCrop()"
                        class="absolute bottom-4 right-44 px-5 z-50 py-2 bg-zinc-600 hover:bg-zinc-900 font-bold text-white rounded-lg hidden">
                        {{ t('Reset') }}
                    </button>
                </div>
            @endif
        @endauth
    </div>

    <div class="mb-24">
        <div class="container mx-auto px-4 md:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-8 -mt-16">
                <div class="relative">
                    {{-- Badge de verificado --}}
                    @if ($user->type === 'verified')
                        <div class="absolute -top-6 right-4 bg-accent p-2 rounded-full text-white shadow-lg z-50">
                            <flux:icon name="crown" class="w-5 h-5" />
                        </div>
                    @endif

                    {{-- Avatar --}}
                    @php
                        $avatar = $user->avatar();
                    @endphp
                    <div
                        class="relative {{ $showProfileRing ? 'profile-ring w-44 h-44 md:w-52 md:h-52' : '' }}"
                        @if ($showProfileRing)
                            style="
                                --ring-gradient: {{ $ringStyle?->gradient ?? 'conic-gradient(from 120deg, #22d3ee, #6366f1, #f97316, #22d3ee)' }};
                                --ring-border: {{ $ringStyle?->border ?? 'rgba(255,255,255,0.25)' }};
                                --ring-speed: {{ $ringStyle?->speed ?? '8s' }};
                                --ring-glow: rgba(56, 189, 248, 0.35);
                            "
                        @endif
                    >
                        <div
                            class="relative z-10 w-44 h-44 md:w-52 md:h-52 border-8 border-zinc-50 dark:border-zinc-800 rounded-full overflow-hidden shadow-[0_20px_60px_-35px_rgba(0,0,0,0.6)]">
                        @if ($avatar['type'] === 'image')
                            <img id="profileAvatarImage" wire:ignore src="{{ $avatarSrc ?? $avatar['value'] }}"
                                class="w-full h-full rounded-full object-cover" alt="{{ t('Avatar') }}">
                        @else
                            <div class="w-full h-full rounded-full bg-zinc-300 dark:bg-zinc-700 text-white flex items-center justify-center text-4xl font-black tracking-wide">
                                {{ $avatar['value'] }}
                            </div>
                        @endif

                        @auth
                            @if (auth()->id() === $user->id)
                                <!-- Overlay hover para alterar avatar -->
                                <label
                                    class="absolute inset-0 flex items-center justify-center bg-black/50 bg-opacity-40 text-white opacity-0 hover:opacity-100 cursor-pointer transition-opacity"
                                    title="{{ t('Click to change the avatar') }}">
                                    <flux:icon name="image" class="w-6 h-6" />
                                    <input type="file" wire:model="avatarTemp"
                                        class="absolute inset-0 opacity-0 cursor-pointer">
                                </label>
                            @endif
                        @endauth
                        </div>
                    </div>

                    {{-- Follow / Unfollow --}}
                    <div class="flex items-center flex-col mt-4">
                        @auth
                            @if (auth()->id() !== $user->id)
                                <flux:button type="button" wire:click="toggleFollow" variant="danger"
                                    class="w-full flex items-center gap-5">
                                    {{ $isFollowing ? t('Unfollow') : t('Follow') }}
                                </flux:button>
                            @endif
                        @endauth
                    </div>
                </div>

                {{-- Conteúdo do Perfil --}}
                <div class="lg:col-span-1">
                    <div class="rounded-2xl border border-zinc-200/70 dark:border-zinc-800/80 bg-white/70 dark:bg-zinc-900/80 backdrop-blur p-6 shadow-[0_20px_60px_-50px_rgba(0,0,0,0.35)]">
                        <h2
                            class="font-bold text-2xl tracking-tight text-zinc-800 dark:text-zinc-50 flex items-center gap-2">
                            {{ $user->name }}

                            @if ($user->is_private)
                                <span
                                    class="px-2 py-0.5 text-xs rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-200 flex items-center gap-1">
                                    <flux:icon name="lock" class="w-3 h-3" />
                                    {{ t('Private') }}
                                </span>
                            @endif
                        </h2>

                        {{-- Seguidores e Seguindo --}}
                        <div class="flex flex-wrap gap-2 text-sm mt-3">
                            @if (!$user->hide_followers)
                                <flux:badge size="sm">{{ $followersCount }} {{ t('Followers') }}</flux:badge>
                            @endif

                            @if (!$user->hide_following)
                                <flux:badge size="sm">{{ $followingCount }} {{ t('Following') }}</flux:badge>
                            @endif
                        </div>

                        {{-- Biografia --}}
                        <p class="italic font-normal text-zinc-600 dark:text-zinc-200 mt-6">
                            {{ $user->biography ?: t('Biography not set') }}
                        </p>

                        <flux:separator class="my-5" />
                    @if ($user->is_private && auth()->id() !== $user->id)
                        <div
                            class="col-span-full flex flex-col items-center justify-center py-10 text-zinc-500 dark:text-zinc-100">
                            <flux:icon name="lock" class="w-10 h-10 mb-3" />
                            <p class="text-lg font-semibold">{{ t('This profile is private') }}</p>
                            <p class="text-sm">{{ t('Follow the user to see their collections and activity.') }}</p>
                        </div>
                    @elseif($user->hide_collections && auth()->id() !== $user->id)
                        <p class="text-zinc-400 italic mt-8 text-xl font-bold mx-auto flex items-center justify-center">
                            {{ t("This user's collections are hidden.") }}</p>
                    @else
                        {{-- Coleções --}}
                        <h2 class="text-xl font-semibold mt-2 mb-6">{{ t('Collections') }}</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($collections as $index => $collection)
                                <x-card-collection :colecao="$collection" :index="$index" />
                            @empty
                                <span class="col-span-full text-zinc-400 mx-auto italic tracking-wide">
                                    {{ t('No collections posted.') }}
                                </span>
                            @endforelse
                        </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
