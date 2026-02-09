<?php

namespace App\Livewire\App\Checkout;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("components.layouts.checkout")]
class OrderPay extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $this->order = $order->load(["items.item"]);
    }

    public function render()
    {
        return view("livewire.app.checkout.order", [
            "order" => $this->order,
        ])->title("Checkout");
    }
}
