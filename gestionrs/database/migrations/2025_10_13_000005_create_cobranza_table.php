<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cobranza', function (Blueprint $table) {
            $table->id('Id_codigo');
            $table->string('Concepto', 100);
            $table->dateTime('Fecha_hora')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->decimal('Cantidad', 10, 2);
            $table->decimal('Precio_unitario', 10, 2);
            $table->unsignedBigInteger('Id_usuario');
            $table->foreign('Id_usuario')->references('Id_usuario')->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobranza');
    }
};
