<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

if (!Capsule::schema()->hasTable('usuarios')) {
    Capsule::schema()->create('usuarios', function (Blueprint $table) {
        $table->bigIncrements('ID_U'); // Changed from increments to bigIncrements
        $table->string('correo')->unique();
        $table->string('contraseña');
        $table->string('nombre');
        $table->string('apellidos');
        $table->boolean('esAdmin')->default(false);
        $table->timestamps();
    });
}
