<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La tabla ya existía sin la columna de correo: Laravel no vuelve a ejecutar
        // una migración que ya corrió, aunque después se le agreguen campos.
        if (! Schema::hasColumn('clientes', 'email')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->string('email')->nullable()->after('telefono');
            });
        }

        // Teléfono y dirección pasan a ser opcionales: al guardar un cliente desde
        // la pantalla de edición no se envía dirección, y daba error.
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('telefono')->nullable()->change();
        });

        if (Schema::hasColumn('clientes', 'direccion')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->string('direccion')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clientes', 'email')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
    }
};
