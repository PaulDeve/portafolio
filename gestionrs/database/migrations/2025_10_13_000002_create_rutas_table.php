<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->id('id_ruta');
            $table->string('nombre_ruta', 100);
            $table->string('zona', 100);
            $table->unsignedBigInteger('id_recolector')->nullable();
            $table->foreign('id_recolector')->references('Id_usuario')->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
