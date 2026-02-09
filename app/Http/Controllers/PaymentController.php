<?php


namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\MercadoPagoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{


    public function index(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
        ]);
        try {
            $service = new MercadoPagoService();
            $result = $service->processPayment([
                'payment_method_id' => $request->formData['payment_method_id'],
                'token' => $request->formData['token'],
                'issuer_id' => $request->formData['issuer_id'],
            ], $user, $plan);

            Payment::create([
                'subscription_id' => $subscription->id,
                'payment_id_mercadopago' => $result['payment']->id,
                'amount' => $plan->price,
                'status' => $result['payment']->status,
                'paid_at' => $result['paid_at'],
            ]);

            if ($result['payment']->status === 'approved') {
                $subscription->update([
                    'status' => 'active',
                    'started_at' => now(),
                    'expires_at' => now()->addMonth(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pagamento realizado com sucesso!',
                'payment' => $result['payment'],
                'status' => $result['payment']->status,  // <-- aqui o payment_id
                'payment_id' => $result['payment']->id,  // <-- aqui o payment_id
                'subscription' => $subscription,
                'redirect_url' => '/',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro no pagamento: ' . $e->getMessage()
            ], 500);
        }
    }
}
