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
        Schema::create('policias', function (Blueprint $table) {
            $table->id();
            $table->string('numero_placa')->unique()->comment('Número de placa del policía');
            $table->string('nombre_completo');
            $table->string('rango')->nullable();
            $table->string('area')->nullable();
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('numero_placa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policias');
    }
};
