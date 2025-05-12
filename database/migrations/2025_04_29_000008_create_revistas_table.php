<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

if (!Capsule::schema()->hasTable('revistas')) {
    Capsule::schema()->create('revistas', function (Blueprint $table) {
        $table->id('ID_Revista');
        $table->string('titulo');
        $table->string('editorial');
        $table->date('fecha_publicacion');
        $table->string('numero');
        $table->text('descripcion')->nullable();
        $table->decimal('precio', 8, 2);
        $table->integer('stock')->default(0);
        $table->string('imagen')->nullable();
        $table->timestamps();
    });
}
