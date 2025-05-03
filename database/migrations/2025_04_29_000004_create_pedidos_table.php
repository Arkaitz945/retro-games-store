<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

if (!Capsule::schema()->hasTable('pedidos')) {
    Capsule::schema()->create('pedidos', function (Blueprint $table) {
        $table->id('ID_Pedido');
        $table->unsignedBigInteger('ID_U');
        $table->dateTime('fecha');
        $table->decimal('total', 10, 2);
        $table->string('estado');
        $table->timestamps();

        $table->foreign('ID_U')->references('ID_U')->on('usuarios');
    });
}
