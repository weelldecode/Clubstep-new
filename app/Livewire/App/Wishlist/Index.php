<?php

namespace App\Livewire\App\Wishlist;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.app")]
class Index extends Component
{
    public function remove(int $itemId): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $user->favorites()->detach($itemId);
        $this->dispatch("notify", message: "Item removido dos favoritos.");
    }

    public function render()
    {
        $user = Auth::user();
        $items = $user ? $user->favorites()->latest()->get() : collect();

        return view("livewire.app.wishlist.index", [
            "items" => $items,
        ])->title("Wishlist");
    }
}
