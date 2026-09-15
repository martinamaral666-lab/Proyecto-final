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
       Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_item');
            $table->string('categoria');
            $table->integer('stock_actual');
            $table->string('unidad');
            $table->string('ubicacion');
            $table->string('estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('inventarios');
    }
};
