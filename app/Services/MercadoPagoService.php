<?php

namespace App\Services;
 
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\Client\Common\RequestOptions;
use DateTime;

class MercadoPagoService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_TOKEN'));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
    }


    public function processPayment(array $data, $user, $plan, $subscription = null, $isRenewal = false)
    { 
        $dateNow = (new DateTime())->format('Y-m-d H:i:s');
        $orderNumber = 'PED' . $dateNow . rand(100, 999);

        $paymentData = [
            'transaction_amount' => floatval($plan->price),
            'description' => $plan->name,
            'payment_method_id' => $data['payment_method_id'],
            'payer' => [
                'email' => $user->email,
                'first_name' => $user->name,
            ],
        ];

        if ($data['payment_method_id'] === 'master') {
            if (empty($data['token'])) {
                throw new \InvalidArgumentException('Card token required');
            }
            $paymentData['token'] = $data['token'];
            $paymentData['installments'] = 1;
            $paymentData['issuer_id'] = $data['issuer_id'];
        }

        $requestOptions = new RequestOptions();
        $requestOptions->setCustomHeaders([
            "X-Idempotency-Key: " . uniqid()
        ]);

        $client = new PaymentClient();

        try {
            $payment = $client->create($paymentData, $requestOptions);

            $paidAt = $payment->status === 'approved' ? $dateNow : null;

            return [
                'payment' => $payment,
                'order_number' => $orderNumber,
                'paid_at' => $paidAt,
                'isRenewal' => $isRenewal,
            ];

        } catch (MPApiException $e) {
            throw new \Exception('Erro ao processar pagamento: ' . $e->getMessage());
        }
    }

    public function processOrderPayment(array $data, $user, $order)
    {
        $dateNow = (new DateTime())->format('Y-m-d H:i:s');
        $orderNumber = 'PED' . $dateNow . rand(100, 999);

        $paymentData = [
            'transaction_amount' => floatval($order->total_amount),
            'description' => 'Compra avulsa #' . $order->id,
            'payment_method_id' => $data['payment_method_id'],
            'payer' => [
                'email' => $user->email,
                'first_name' => $user->name,
            ],
        ];

        if ($data['payment_method_id'] === 'master') {
            if (empty($data['token'])) {
                throw new \InvalidArgumentException('Card token required');
            }
            $paymentData['token'] = $data['token'];
            $paymentData['installments'] = 1;
            $paymentData['issuer_id'] = $data['issuer_id'];
        }

        $requestOptions = new RequestOptions();
        $requestOptions->setCustomHeaders([
            "X-Idempotency-Key: " . uniqid()
        ]);

        $client = new PaymentClient();

        try {
            $payment = $client->create($paymentData, $requestOptions);

            $paidAt = $payment->status === 'approved' ? $dateNow : null;

            return [
                'payment' => $payment,
                'order_number' => $orderNumber,
                'paid_at' => $paidAt,
                'isRenewal' => false,
            ];

        } catch (MPApiException $e) {
            throw new \Exception('Erro ao processar pagamento: ' . $e->getMessage());
        }
    }
}
