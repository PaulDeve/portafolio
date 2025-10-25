<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('Id_usuario');
            $table->string('nombre_usuario', 50);
            $table->string('correo', 100)->unique();
            $table->string('contrasena', 255);
            $table->enum('rol', ['admin', 'recolector', 'vecino'])->default('vecino');
            $table->dateTime('fecha_registro')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
