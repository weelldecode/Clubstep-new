<?php

namespace App\Livewire\Components;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartBadge extends Component
{
    protected $listeners = [
        "cart-updated" => "refreshCount",
    ];

    public function refreshCount(): void
    {
        // just trigger re-render
    }

    private function countItems(): int
    {
        if (!Auth::check()) {
            return 0;
        }

        $cart = Cart::query()
            ->with("items")
            ->where("user_id", Auth::id())
            ->where("status", "active")
            ->first();

        if (!$cart) {
            return 0;
        }

        return (int) $cart->items->sum("quantity");
    }

    public function render()
    {
        return view("livewire.components.cart-badge", [
            "count" => $this->countItems(),
        ]);
    }
}
