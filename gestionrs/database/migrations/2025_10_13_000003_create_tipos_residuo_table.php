<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tipos_residuo', function (Blueprint $table) {
            $table->id('id_residuo');
            $table->string('nombre', 100);
            $table->enum('categoria', ['orgánico','inorgánico','reciclable','peligroso'])->default('reciclable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_residuo');
    }
};
