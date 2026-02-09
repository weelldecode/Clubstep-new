<div class="relative">
    <flux:dropdown align="end">
    <flux:button icon:trailing="bell" variant="ghost"> </flux:button>
    <flux:menu>

        @forelse($notifications as $notification)
            <div class="px-4 py-2 border-b">
                @if(!empty($notification->data['url']))
                    <a href="{{ $notification->data['url'] }}" class="text-sm text-zinc-800 dark:text-zinc-100 hover:underline">
                        {{ $notification->data['message'] }}
                    </a>
                @else
                    {{ $notification->data['message'] }}
                @endif
                <flux:menu.item wire:click="markAsRead('{{ $notification->id }}')" class="text-xs text-blue-500">Marcar lida</flux:menu.item>
            </div>
            <flux:menu.separator />
        @empty
            <div class="px-4 py-2 text-zinc-500 dark:text-zinc-100">Sem notificações</div>
        @endforelse
    </flux:menu>
</flux:dropdown>
</div>
