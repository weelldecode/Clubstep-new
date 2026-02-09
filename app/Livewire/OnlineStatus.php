<?php

namespace App\Livewire;

use App\Livewire\Actions\Logout;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class OnlineStatus extends Component
{

  public function updateLastSeen()
    {
        if(auth()->check()) {
            auth()->user()->update(['last_seen_at' => now()]);
        }
    }

    public function render()
    {
        $onlineUsers = User::where('last_seen_at', '>=', now()->subMinutes(5))->get();

        return view('livewire.online-status', compact('onlineUsers'));
    }
}
