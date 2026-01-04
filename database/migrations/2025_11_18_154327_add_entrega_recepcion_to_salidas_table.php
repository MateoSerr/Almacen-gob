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
        Schema::table('salidas', function (Blueprint $table) {
            $table->string('entrega_nombre')->nullable()->after('observaciones');
            $table->text('entrega_firma')->nullable()->after('entrega_nombre');
            $table->string('recibe_nombre')->nullable()->after('entrega_firma');
            $table->text('recibe_firma')->nullable()->after('recibe_nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salidas', function (Blueprint $table) {
            $table->dropColumn(['entrega_nombre', 'entrega_firma', 'recibe_nombre', 'recibe_firma']);
        });
    }
};
