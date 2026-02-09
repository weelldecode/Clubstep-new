<?php

namespace App\Livewire\App\Cart;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.app")]
class Index extends Component
{
    public ?Cart $cart = null;
    public array $items = [];

    public function mount(): void
    {
        $this->loadCart();
    }

    private function loadCart(): void
    {
        $this->cart = Cart::query()
            ->with(["items.item"])
            ->where("user_id", Auth::id())
            ->where("status", "active")
            ->first();

        $this->items = $this->cart?->items?->values()->all() ?? [];
    }

    public function removeItem(int $itemId): void
    {
        if (!$this->cart) {
            return;
        }

        $this->cart->items()->where("item_id", $itemId)->delete();
        $this->loadCart();
        $this->dispatch("notify", message: "Item removido do carrinho.");
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        if (!$this->cart) {
            return;
        }

        $quantity = 1;

        $this->cart->items()->where("item_id", $itemId)->update([
            "quantity" => $quantity,
        ]);

        $this->loadCart();
    }

    public function checkout(): void
    {
        if (!$this->cart || empty($this->items)) {
            $this->dispatch("notify", message: "Seu carrinho está vazio.");
            return;
        }

        $total = 0;

        $order = Order::create([
            "user_id" => Auth::id(),
            "status" => "pending",
            "total_amount" => 0,
        ]);

        foreach ($this->items as $cartItem) {
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

        $this->cart->items()->delete();
        $this->cart->update(["status" => "converted"]);

        redirect()->route("checkout.order", $order);
    }

    public function render()
    {
        $total = 0;
        foreach ($this->items as $cartItem) {
            $total += (float) $cartItem->price * (int) $cartItem->quantity;
        }

        return view("livewire.app.cart.index", [
            "cart" => $this->cart,
            "items" => $this->items,
            "total" => $total,
        ])->title("Carrinho");
    }
}
