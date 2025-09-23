<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('columna_id');
            $table->string('nombre', 255);
            $table->string('descripcion', 255)->nullable();
            $table->date('fecha_asignacion');
            $table->date('fecha_limite');
            $table->string('usuario_asignador', 255);
            $table->string('usuario_asignado', 255);
            $table->integer('avance')->default(0);
            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');
            $table->integer('posicion');
            $table->timestamps();

            // Relación con columnas
            $table->foreign('columna_id')
                  ->references('id')->on('columnas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
