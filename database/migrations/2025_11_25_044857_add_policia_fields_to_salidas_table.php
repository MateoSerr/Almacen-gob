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
            $table->boolean('es_entrega_policia')->default(false)->after('user_id')->comment('Indica si es una entrega a policía');
            $table->foreignId('policia_id')->nullable()->after('es_entrega_policia')->constrained('policias')->onDelete('set null');
            
            $table->index('es_entrega_policia');
            $table->index('policia_id');
            // Índice único compuesto para evitar entregas duplicadas del mismo producto al mismo policía
            $table->unique(['producto_id', 'policia_id'], 'unique_producto_policia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salidas', function (Blueprint $table) {
            $table->dropUnique('unique_producto_policia');
            $table->dropIndex(['es_entrega_policia']);
            $table->dropIndex(['policia_id']);
            $table->dropForeign(['policia_id']);
            $table->dropColumn(['es_entrega_policia', 'policia_id']);
        });
    }
};
