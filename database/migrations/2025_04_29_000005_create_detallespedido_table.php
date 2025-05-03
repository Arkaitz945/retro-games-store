<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

if (!Capsule::schema()->hasTable('detallespedido')) {
    Capsule::schema()->create('detallespedido', function (Blueprint $table) {
        $table->id('ID_Detalle');
        $table->unsignedBigInteger('ID_Pedido');
        $table->unsignedBigInteger('ID_J');
        $table->integer('cantidad');
        $table->decimal('precioUnitario', 8, 2);
        $table->timestamps();

        $table->foreign('ID_Pedido')->references('ID_Pedido')->on('pedidos');
        $table->foreign('ID_J')->references('ID_J')->on('juegos');
    });
}
