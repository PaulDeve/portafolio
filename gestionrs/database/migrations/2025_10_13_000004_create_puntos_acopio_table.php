<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('puntos_acopio', function (Blueprint $table) {
            $table->id('id_punto');
            $table->string('nombre_punto', 100);
            $table->string('direccion', 150)->nullable();
            $table->decimal('capacidad_max', 10, 2)->nullable();
            $table->string('encargado', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puntos_acopio');
    }
};
