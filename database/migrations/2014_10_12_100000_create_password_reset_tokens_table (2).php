<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table-> id();
            $table->string('nombre');
            $table->string('descripcion');
            $table->integer('precio');
            $table->integer('cantidad_disponible');
            $table->unsignedBigInteger('categoria_id');
            $table->unsignedBigInteger('marca_id');
            $table->timestamps();

            

            //CLAVES FORÁNEAS
            $table->foreign('categoria_id')-> references('id')->on('categorias');
            $table->foreign('marca_id')-> references('id')->on('marcas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
