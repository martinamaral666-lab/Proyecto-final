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
        Schema::table('cobros', function (Blueprint $table) {
            $table->string('tipo_registro')->default('venta')->after('id');
            $table->foreignId('user_id')
                ->nullable()
                ->after('tipo_registro')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cobros', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['tipo_registro', 'user_id']);
        });
    }
};