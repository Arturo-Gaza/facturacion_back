<?php
// database/migrations/2025_10_23_000002_create_solicitud_dato_giro_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudDatoGiroTable extends Migration
{
    public function up()
    {
        Schema::create('solicitud_dato_giro', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_solicitud');
            $table->unsignedBigInteger('id_dato_por_giro');
            $table->text('valor')->nullable();
            $table->timestamps();

            $table->foreign('id_solicitud')->references('id')->on('solicitudes')->onDelete('cascade');
            $table->foreign('id_dato_por_giro')->references('id')->on('cat_datos_por_giro')->onDelete('cascade');

            $table->unique(['id_solicitud','id_dato_por_giro']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('solicitud_dato_giro');
    }
}
