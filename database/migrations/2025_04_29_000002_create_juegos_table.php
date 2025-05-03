<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

if (!Capsule::schema()->hasTable('juegos')) {
    Capsule::schema()->create('juegos', function (Blueprint $table) {
        $table->id('ID_J');
        $table->string('nombre');
        $table->string('plataforma');
        $table->string('genero');
        $table->decimal('precio', 8, 2);
        $table->timestamps();
    });
}
