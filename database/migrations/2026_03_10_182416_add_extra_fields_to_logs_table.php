<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->string('ipmquiona')->nullable();
            $table->text('mensaje')->nullable();
            $table->string('estado')->default('pendiente');
            $table->string('tipo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn(['ipmquiona', 'mensaje', 'estado', 'tipo']);
        });
    }
};
    