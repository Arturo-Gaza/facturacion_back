<?php
// database/migrations/2025_10_23_000001_create_datos_por_giro_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatosPorGiroTable extends Migration
{
    public function up()
    {
        Schema::create('cat_datos_por_giro', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_giro');
            $table->string('nombre_dato_giro'); // ejemplo: "numero_caseta"
            $table->string('label')->nullable(); // etiqueta amigable: "Número de caseta"
            $table->string('tipo')->default('string'); // string, numeric, date, boolean, url, etc
            $table->boolean('requerido')->default(false);
            $table->timestamps();

            $table->foreign('id_giro')->references('id')->on('cat_giros')->onDelete('cascade');
            $table->unique(['id_giro','nombre_dato_giro']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('datos_por_giro');
    }
}
