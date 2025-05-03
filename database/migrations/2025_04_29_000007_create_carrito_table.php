<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

if (!Capsule::schema()->hasTable('carrito')) {
    Capsule::schema()->create('carrito', function (Blueprint $table) {
        $table->id('ID_Carrito');
        $table->unsignedBigInteger('ID_U');
        $table->unsignedBigInteger('ID_J');
        $table->integer('cantidad');
        $table->timestamps();

        $table->foreign('ID_U')->references('ID_U')->on('usuarios');
        $table->foreign('ID_J')->references('ID_J')->on('juegos');
    });
}
