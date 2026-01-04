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
        Schema::create('oficios_entrada', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_oficio')->unique(); // Número consecutivo del oficio
            $table->string('folio_completo')->unique(); // FE.19.01/568/2025/CGA
            $table->date('fecha_oficio');
            $table->text('descripcion_material'); // (122 PIEZA) CAJAS DE PLASTICO...
            $table->date('fecha_recepcion');
            $table->string('proveedor_nombre');
            $table->string('numero_factura');
            $table->decimal('importe_total', 12, 2);
            $table->text('importe_total_letra'); // Importe en letras
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('fecha_oficio');
            $table->index('numero_oficio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oficios_entrada');
    }
};
