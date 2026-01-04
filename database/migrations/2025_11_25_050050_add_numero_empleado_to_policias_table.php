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
        Schema::table('policias', function (Blueprint $table) {
            $table->string('numero_empleado')->nullable()->unique()->after('numero_placa')->comment('Número de empleado del policía (único)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policias', function (Blueprint $table) {
            $table->dropIndex(['numero_empleado']);
            $table->dropColumn('numero_empleado');
        });
    }
};
