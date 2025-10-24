<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeFieldsToMovimientosSaldoTable extends Migration
{
    public function up()
    {
        Schema::table('movimientos_saldo', function (Blueprint $table) {
            // Identificadores de Stripe
            $table->string('payment_intent_id')->nullable()->unique()->after('descripcion');
            $table->string('stripe_charge_id')->nullable()->after('payment_intent_id');
            $table->string('customer_id')->nullable()->after('stripe_charge_id');
            $table->string('payment_method')->nullable()->after('customer_id');

            // Moneda y montos exactos
            $table->string('currency', 3)->default('mxn')->after('monto');
            $table->bigInteger('amount_cents')->nullable()->after('monto')
                ->comment('Monto en centavos (consistencia con Stripe)');

            // Datos para idempotencia/conciliación/auditoría
            $table->json('metadata')->nullable()->after('payment_method');
            $table->string('stripe_event_id')->nullable()->after('metadata');
            $table->text('webhook_payload')->nullable()->after('stripe_event_id');

            // Resultado/procesamiento
            $table->timestamp('processed_at')->nullable()->after('webhook_payload');
            $table->string('failure_code')->nullable()->after('processed_at');
            $table->text('failure_message')->nullable()->after('failure_code');

            // Opcionales
            $table->string('idempotency_key')->nullable()->after('failure_message');
            $table->decimal('refunded_amount', 10, 2)->default(0)->after('nuevo_monto');
            $table->boolean('reverted')->default(false)->after('refunded_amount');
        });
    }

    public function down()
    {
        Schema::table('movimientos_saldo', function (Blueprint $table) {
            $table->dropColumn([
                'payment_intent_id',
                'stripe_charge_id',
                'customer_id',
                'payment_method',
                'currency',
                'amount_cents',
                'metadata',
                'stripe_event_id',
                'webhook_payload',
                'processed_at',
                'failure_code',
                'failure_message',
                'idempotency_key',
                'refunded_amount',
                'reverted',
            ]);
        });
    }
}
