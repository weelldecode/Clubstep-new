@php
    $totalDownloads = $downloads->count();
    $readyDownloads = $downloads->where("status", "ready")->count();
    $processingDownloads = $downloads->where("status", "processing")->count();
@endphp

<div class="relative px-4 md:px-8">
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp .6s ease-out both; }
        .delay-1 { animation-delay: .08s; }
        .delay-2 { animation-delay: .16s; }
    </style>

    <section class="container mx-auto mt-6 mb-8 fade-up">
        <livewire:components.breadcrumb />

        <div class="mt-3 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">{{ t('My Downloads') }}</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ t('Files downloaded from collections and current availability status.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="inline-flex items-center rounded-full bg-white/70 px-3 py-1 font-semibold text-zinc-700 ring-1 ring-zinc-200/60 backdrop-blur dark:bg-zinc-900/80 dark:text-zinc-200 dark:ring-zinc-800">
                    {{ $totalDownloads }} {{ t('Total') }}
                </span>
                <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-1 font-semibold text-emerald-600 ring-1 ring-emerald-500/20">
                    {{ $readyDownloads }} {{ t('Ready') }}
                </span>
                <span class="inline-flex items-center rounded-full bg-amber-500/10 px-3 py-1 font-semibold text-amber-600 ring-1 ring-amber-500/20">
                    {{ $processingDownloads }} {{ t('Processing') }}
                </span>
            </div>
        </div>

        <flux:separator class="mt-6" variant="subtle" />
    </section>

    <section class="container mx-auto pb-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($downloads as $index => $download)
                @php
                    $collection = $download->collection;
                    $status = strtolower((string) $download->status);
                    $isReady = $status === "ready" && !empty($download->file_path);
                    $isExternal = $isReady && \Illuminate\Support\Str::startsWith((string) $download->file_path, ["http://", "https://"]);
                    $statusLabel = t($status ? ucfirst($status) : "Unknown");

                    if ($status === "ready") {
                        $statusClass = "bg-emerald-500/10 text-emerald-600 ring-emerald-500/20";
                    } elseif ($status === "processing") {
                        $statusClass = "bg-amber-500/10 text-amber-600 ring-amber-500/20";
                    } else {
                        $statusClass = "bg-zinc-500/10 text-zinc-600 ring-zinc-500/20 dark:text-zinc-300";
                    }
                @endphp

                <article
                    x-data="{ visible: false }"
                    x-init="setTimeout(() => visible = true, {{ $index * 60 }})"
                    :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-3'"
                    class="fade-up delay-1 overflow-hidden rounded-2xl border border-zinc-200/70 bg-white/80 shadow-[0_20px_50px_-40px_rgba(0,0,0,0.45)] backdrop-blur transition-all duration-300 hover:-translate-y-1 dark:border-zinc-800/80 dark:bg-zinc-900/80"
                >
                    <a href="{{ $collection ? '/collection/v/' . $collection->slug : '#' }}" class="block">
                        <img
                            src="{{ $collection ? $collection->cover_url : asset('images/placeholders/collection-default.svg') }}"
                            alt="{{ $collection?->name ?? t('Collection removed') }}"
                            class="h-52 w-full object-cover"
                            loading="lazy"
                        />
                    </a>

                    <div class="space-y-3 p-4">
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="line-clamp-1 text-base font-bold tracking-tight text-zinc-800 dark:text-zinc-100">
                                {{ $collection?->name ?? t('Collection removed') }}
                            </h2>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $download->created_at?->diffForHumans() }}
                        </p>

                        @if ($isReady)
                            <flux:button
                                href="{{ $isExternal ? $download->file_path : \Illuminate\Support\Facades\Storage::url($download->file_path) }}"
                                variant="primary"
                                class="w-full"
                            >
                                {{ t('Download file') }}
                            </flux:button>
                        @else
                            <div class="flex items-center justify-center rounded-lg border border-zinc-200/70 bg-zinc-100/70 px-3 py-2 text-sm font-medium text-zinc-600 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300">
                                {{ t('Processing download...') }}
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="col-span-full fade-up delay-2">
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-zinc-200/70 bg-white/80 px-6 py-20 text-center backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/70">
                        <flux:icon name="download" class="mb-3 h-10 w-10 text-accent" />
                        <p class="text-lg font-semibold text-zinc-700 dark:text-zinc-100">{{ t('You have not downloaded anything yet.') }}</p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-300">
                            {{ t('Explore collections and start your first download.') }}
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>
</div>
