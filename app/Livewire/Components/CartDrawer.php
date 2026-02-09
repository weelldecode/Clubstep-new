<?php

namespace App\Livewire\Components;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartDrawer extends Component
{
    protected $listeners = [
        "cart-updated" => '$refresh',
    ];
    public function removeItem(int $itemId): void
    {
        $cart = $this->activeCart();
        if (!$cart) {
            return;
        }

        $cart->items()->where("item_id", $itemId)->delete();
        $this->dispatch("notify", message: "Item removido do carrinho.");
        $this->dispatch("cart-updated");
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $cart = $this->activeCart();
        if (!$cart) {
            return;
        }

        $quantity = 1;

        $cart->items()->where("item_id", $itemId)->update([
            "quantity" => $quantity,
        ]);
        $this->dispatch("cart-updated");
    }

    public function checkout(): void
    {
        $cart = $this->activeCart();
        if (!$cart || $cart->items->isEmpty()) {
            $this->dispatch("notify", message: "Seu carrinho está vazio.");
            return;
        }

        $total = 0;

        $order = Order::create([
            "user_id" => Auth::id(),
            "status" => "pending",
            "total_amount" => 0,
        ]);

        foreach ($cart->items as $cartItem) {
            $item = $cartItem->item;

            if (!$item || $item->type !== "sites") {
                continue;
            }

            $lineTotal = (float) $cartItem->price * (int) $cartItem->quantity;
            $total += $lineTotal;

            OrderItem::create([
                "order_id" => $order->id,
                "item_id" => $item->id,
                "price" => $cartItem->price,
                "quantity" => $cartItem->quantity,
                "total" => $lineTotal,
            ]);
        }

        $order->update([
            "total_amount" => $total,
        ]);

        $cart->items()->delete();
        $cart->update(["status" => "converted"]);
        $this->dispatch("cart-updated");

        redirect()->route("checkout.order", $order);
    }

    private function activeCart(): ?Cart
    {
        if (!Auth::check()) {
            return null;
        }

        return Cart::query()
            ->with(["items.item"])
            ->where("user_id", Auth::id())
            ->where("status", "active")
            ->first();
    }

    public function render()
    {
        $cart = $this->activeCart();
        $items = $cart?->items ?? collect();
        $total = 0;

        foreach ($items as $cartItem) {
            $total += (float) $cartItem->price * (int) $cartItem->quantity;
        }

        return view("livewire.components.cart-drawer", [
            "cart" => $cart,
            "items" => $items,
            "total" => $total,
        ]);
    }
}
