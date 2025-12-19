<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 10-А, 11-Б и т.д.
            $table->integer('level')->nullable(); // 10, 11 класс
            $table->integer('capacity')->default(30);
            $table->string('room_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};

