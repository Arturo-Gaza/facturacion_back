<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentAndFeeFieldsToMovimientosSaldoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movimientos_saldo', function (Blueprint $table) {
            // Información del método de pago
            $table->string('payment_method_type')->nullable()->after('payment_method'); // ej. 'card', 'oxxo', 'bank_transfer'
            $table->string('card_brand')->nullable()->after('payment_method_type'); // ej. 'visa', 'mastercard'
            $table->string('card_last4', 4)->nullable()->after('card_brand'); // últimos 4 digitos

            // Saldos
            $table->decimal('saldo_antes', 12, 2)->nullable()->after('saldo_resultante');

            // Comisiones / fees / neto
            $table->decimal('fees_amount', 12, 2)->nullable()->after('saldo_antes'); // monto de comisiones (en la misma currency)
            $table->string('fees_currency', 10)->nullable()->after('fees_amount'); // moneda de las fees (por si aplica)
            $table->decimal('net_amount', 12, 2)->nullable()->after('fees_currency'); // monto neto que llega a cuenta
            $table->json('fees_raw')->nullable()->after('net_amount'); // objeto JSON completo para auditoría (BalanceTransaction, fee_details, etc.)

            // Índices útiles para búsquedas rápidas
            $table->index('payment_method');
            $table->index('payment_intent_id');
            $table->index('card_last4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movimientos_saldo', function (Blueprint $table) {
            $table->dropIndex(['payment_method']);
            $table->dropIndex(['payment_intent_id']);
            $table->dropIndex(['card_last4']);

            $table->dropColumn([
                'payment_method_type',
                'card_brand',
                'card_last4',
                'saldo_antes',
                'fees_amount',
                'fees_currency',
                'net_amount',
                'fees_raw',
            ]);
        });
    }
}
