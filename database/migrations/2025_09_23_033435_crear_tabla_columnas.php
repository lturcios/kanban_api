<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('columnas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tablero_id');
            $table->string('titulo', 255);
            $table->integer('posicion');
            $table->string('color', 255);
            $table->timestamps();

            // Relación con tablero
            $table->foreign('tablero_id')
                  ->references('id')->on('tablero')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('columnas');
    }
};
