<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

if (!Capsule::schema()->hasTable('direccion')) {
    Capsule::schema()->create('direccion', function (Blueprint $table) {
        $table->id('ID_Direccion');
        $table->string('calle');
        $table->string('numero');
        $table->string('codigoPostal');
        $table->unsignedBigInteger('idUsuario');
        $table->timestamps();

        $table->foreign('idUsuario')->references('ID_U')->on('usuarios');
    });
}
