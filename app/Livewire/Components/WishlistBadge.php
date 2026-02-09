<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WishlistBadge extends Component
{
    protected $listeners = [
        "wishlist-updated" => "refreshCount",
    ];

    public function refreshCount(): void
    {
        // trigger re-render
    }

    private function countItems(): int
    {
        $user = Auth::user();
        if (!$user) {
            return 0;
        }

        return (int) $user->favorites()->count();
    }

    public function render()
    {
        return view("livewire.components.wishlist-badge", [
            "count" => $this->countItems(),
        ]);
    }
}
