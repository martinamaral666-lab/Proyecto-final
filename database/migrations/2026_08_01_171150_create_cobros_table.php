<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

      Schema::create('cobros', function (Blueprint $table) {
        $table->id();
        $table->string('nombre_cliente');
        $table->string('telefono');
        $table->string('concepto');
        $table->decimal('monto', 10, 2);
        $table->string('mano_de_obra');
        $table->text('motivo_no_realizado')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobros');
    }
};

