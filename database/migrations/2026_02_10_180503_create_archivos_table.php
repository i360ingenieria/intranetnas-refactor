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
        Schema::create('archivos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->text('ruta');
        $table->enum('tipo', ['archivo', 'carpeta']);
        $table->string('extension')->nullable();
        $table->unsignedBigInteger('size');
        $table->timestamp('modified');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos');
    }
};
