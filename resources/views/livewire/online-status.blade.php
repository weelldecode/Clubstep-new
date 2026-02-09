<div wire:poll.60000ms="updateLastSeen">
    <h3>{{ t('Online users') }}:</h3>
    @foreach($onlineUsers as $user)
        <div class="flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-green-500"></span>
            <span>{{ $user->name }}</span>
        </div>
    @endforeach
</div>
