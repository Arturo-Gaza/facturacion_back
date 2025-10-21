use Illuminate\Http\Request;
use Stripe\Webhook;

public function webhook(Request $request)
{
    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    $endpointSecret = env('STRIPE_WEBHOOK_SECRET'); // obtenido en dashboard o stripe listen

    try {
        $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
    } catch (\UnexpectedValueException $e) {
        return response('Invalid payload', 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        return response('Invalid signature', 400);
    }

    // Maneja eventos:
    if ($event->type === 'payment_intent.succeeded') {
        $pi = $event->data->object;
        // marcar orden como pagada, enviar email, etc.
    }

    return response('OK', 200);
}
