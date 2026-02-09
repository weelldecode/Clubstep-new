<flux:breadcrumbs class="mb-5 animate__animated animate__fadeIn">
    @foreach ($items as $item)
        @php 
            $isLast = $loop->last;
        @endphp

        @if ($item['url'] && !$isLast)
            <flux:breadcrumbs.item href="{{ $item['url'] }}" separator="slash"> {{ t($item['label']) }}</flux:breadcrumbs.item>
        @elseif ($isLast)
            <flux:breadcrumbs.item separator="slash"  > {{ t($item['label']) }}</flux:breadcrumbs.item>
        @else
            <flux:breadcrumbs.item separator="slash"> {{ t($item['label']) }}</flux:breadcrumbs.item>
        @endif

        @if (!$loop->last)
        @endif
    @endforeach
</flux:breadcrumbs>
