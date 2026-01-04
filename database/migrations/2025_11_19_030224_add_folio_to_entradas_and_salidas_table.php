<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->string('folio')->unique()->nullable()->after('id');
        });

        Schema::table('salidas', function (Blueprint $table) {
            $table->string('folio')->unique()->nullable()->after('id');
        });

        // Generar folios para registros existentes
        DB::statement("UPDATE entradas SET folio = CONCAT('FISCALÍA ESTATAL (FÍSICO).IN/', YEAR(fecha), '/', LPAD(id, 5, '0')) WHERE folio IS NULL");
        DB::statement("UPDATE salidas SET folio = CONCAT('FISCALÍA ESTATAL (FÍSICO).OUT/', YEAR(fecha), '/', LPAD(id, 5, '0')) WHERE folio IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn('folio');
        });

        Schema::table('salidas', function (Blueprint $table) {
            $table->dropColumn('folio');
        });
    }
};
