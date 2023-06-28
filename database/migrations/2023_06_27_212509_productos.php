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
            $table->id();
            $table->string('nombre', 255);
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->string('EAN', 30);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->string('incentivo', 20);
            $table->timestamps();

            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('set null');
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