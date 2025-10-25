<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago');
            $table->unsignedBigInteger('id_cobranza');
            $table->enum('tipo_pago', ['efectivo','tarjeta','transferencia'])->default('efectivo');
            $table->decimal('monto', 10, 2);
            $table->dateTime('fecha_pago')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('id_cobranza')->references('Id_codigo')->on('cobranza');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
