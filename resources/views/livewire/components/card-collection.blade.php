@props(['colecao'])

<div class="bg-white rounded-2xl shadow hover:shadow-lg transition overflow-hidden">
    {{-- Capa da coleção (se tiver item de destaque, pode puxar thumb dele) --}}
    <div class="aspect-video bg-gray-200">
        <img src="{{ $colecao->cover_url }}"
             alt="{{ $colecao->name }}"
             class="w-full h-full object-cover">
    </div>

    {{-- Conteúdo --}}
    <div class="p-4">
        <h3 class="text-lg font-semibold truncate">
            <a href="{{ route('collections.show', $colecao->slug) }}" class="hover:underline">
                {{ $colecao->name }}
            </a>
        </h3>

        @if($colecao->description)
            <p class="text-sm text-gray-600 line-clamp-2 mt-1">
                {{ $colecao->description }}
            </p>
        @endif

        {{-- Autor --}}
        <div class="flex items-center gap-2 mt-3">
            @php
                $avatar = $colecao->user->avatar();
            @endphp

            @if($avatar['type'] === 'image')
                <img src="{{ $avatar['value'] }}"
                     alt="{{ $colecao->user->name }}"
                     class="w-8 h-8 rounded-full object-cover">
            @else
                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-sm font-bold text-gray-700">
                    {{ $avatar['value'] }}
                </div>
            @endif

            <span class="text-sm text-gray-700">{{ $colecao->user->name }}</span>
        </div>
    </div>
</div>
