<?php

namespace App\Livewire\App\Checkout;

use Livewire\Component;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Auth;
use Exception;
use Livewire\Attributes\Layout;

#[Layout("components.layouts.checkout")]
class Pay extends Component
{
    public $plan;
    public $payment_method_id;
    public $token;
    public $issuer_id;

    public function pay()
    {
        $this->validate([
            "payment_method_id" => "required",
        ]);

        $user = Auth::user();

        $subscription = Subscription::create([
            "user_id" => $user->id,
            "plan_id" => $this->plan->id,
            "status" => "pending",
        ]);

        try {
            $service = new MercadoPagoService();
            $result = $service->processPayment(
                [
                    "payment_method_id" => $this->payment_method_id,
                    "token" => $this->token,
                    "issuer_id" => $this->issuer_id,
                ],
                $user,
                $this->plan,
            );

            Payment::create([
                "subscription_id" => $subscription->id,
                "payment_id_mercadopago" => $result["payment"]->id,
                "amount" => $this->plan->price,
                "status" => $result["payment"]->status,
                "paid_at" => $result["paid_at"],
            ]);

            if ($result["payment"]->status === "approved") {
                $subscription->update([
                    "status" => "active",
                    "started_at" => now(),
                    "expires_at" => now()->addMonth(),
                ]);
            }

            session()->flash("success", "Pagamento realizado com sucesso!");
            return redirect()->route("dashboard");
        } catch (Exception $e) {
            session()->flash("error", $e->getMessage());
        }
    }

    public function mount($id = null)
    {
        $this->plan = Plan::find($id);
    }

    public function render()
    {
        return view("livewire.app.checkout.pay", [
            "plan" => $this->plan,
        ])->title("Checkout");
    }
}
