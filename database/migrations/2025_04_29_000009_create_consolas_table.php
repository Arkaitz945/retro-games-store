<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

if (!Capsule::schema()->hasTable('consolas')) {
    Capsule::schema()->create('consolas', function (Blueprint $table) {
        $table->id('ID_Consola');
        $table->string('nombre');
        $table->string('fabricante');
        $table->integer('año_lanzamiento');
        $table->text('descripcion')->nullable();
        $table->string('estado');
        $table->decimal('precio', 8, 2);
        $table->integer('stock')->default(0);
        $table->string('imagen')->nullable();
        $table->timestamps();
    });
}
