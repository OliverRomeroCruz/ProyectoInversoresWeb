<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('nombre');
            $table->text('descripcion');
            $table->string('imagen_url');
            $table->string('video_url')->nullable();

            $table->decimal('min_inversion', 10, 2);
            $table->decimal('max_inversion', 10, 2);
            $table->decimal('inversion_actual', 10, 2)->default(0);

            $table->date('fecha_fin');

            $table->string('estado')->default('Pendiente');

            $table->timestamps();
        });
    }
};
