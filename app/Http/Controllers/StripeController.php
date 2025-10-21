<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseHelper;
use App\Models\CatPlanesPrepago;
use Exception;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeController extends Controller
{
    public function createPaymentIntent(Request $req)
    {
        $amount = $req->input('amount'); // en centavos: $10 USD => 1000
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $amount,
            'currency' => $req->input('currency', 'usd'),
            // opcional: 'metadata' => ['order_id' => 1234]
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }

    public function crearPagoByPrepago(Request $req)
    {
        try {
            $idPrepago = $req->input('idPrepago'); // en centavos: $10 USD => 1000
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $prepago = CatPlanesPrepago::find($idPrepago);
            $amount = $prepago->monto * 100;
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => env('DIVISA'),
                // opcional: 'metadata' => ['order_id' => 1234]
            ]);
            return ApiResponseHelper::sendResponse($paymentIntent->id, 'Pago creado correctamente para el plan de prepago.', 200);
        } catch (Exception $ex) {
            return ApiResponseHelper::rollback($ex, $ex->getMessage(), 500);
        }
    }
}
