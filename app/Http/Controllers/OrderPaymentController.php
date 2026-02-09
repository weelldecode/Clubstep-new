<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\MercadoPagoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderPaymentController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            "order_id" => "required|exists:orders,id",
            "payment_method_id" => "required_without:formData.payment_method_id",
            "formData.payment_method_id" => "required_without:payment_method_id",
        ]);

        $user = Auth::user();
        $order = Order::where("id", $request->order_id)
            ->where("user_id", $user->id)
            ->firstOrFail();

        if ($order->status === "paid") {
            return response()->json([
                "success" => true,
                "message" => "Pedido já está pago.",
                "status" => "approved",
                "payment_id" => null,
                "redirect_url" => route("cart.index"),
            ]);
        }

        try {
            $formData = (array) $request->input("formData", []);
            $paymentMethodId = $formData["payment_method_id"] ?? $request->input("payment_method_id");
            $token = $formData["token"] ?? $request->input("token");
            $issuerId = $formData["issuer_id"] ?? $request->input("issuer_id");

            $service = new MercadoPagoService();
            $result = $service->processOrderPayment([
                "payment_method_id" => $paymentMethodId,
                "token" => $token,
                "issuer_id" => $issuerId,
            ], $user, $order);

            Payment::create([
                "order_id" => $order->id,
                "payment_id_mercadopago" => $result["payment"]->id,
                "amount" => $order->total_amount,
                "status" => $result["payment"]->status,
                "paid_at" => $result["paid_at"],
            ]);

            if ($result["payment"]->status === "approved") {
                $order->update([
                    "status" => "paid",
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Pagamento realizado com sucesso!",
                "payment" => $result["payment"],
                "status" => $result["payment"]->status,
                "payment_id" => $result["payment"]->id,
                "order" => $order,
                "redirect_url" => route("cart.index"),
            ]);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro no pagamento: " . $e->getMessage(),
            ], 500);
        }
    }
}
