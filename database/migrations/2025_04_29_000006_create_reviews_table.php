<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

if (!Capsule::schema()->hasTable('reviews')) {
    Capsule::schema()->create('reviews', function (Blueprint $table) {
        $table->bigIncrements('ID_Review'); // Changed from id to bigIncrements
        $table->unsignedBigInteger('ID_U');
        $table->unsignedBigInteger('ID_J');
        $table->tinyInteger('calificacion'); // de 1 a 5, por ejemplo
        $table->text('comentario')->nullable();
        $table->dateTime('fecha');
        $table->timestamps();

        // Make sure foreign keys reference the correct column types
        $table->foreign('ID_U')->references('ID_U')->on('usuarios');
        $table->foreign('ID_J')->references('ID_J')->on('juegos');
    });
}
