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
        Schema::create('pokemon', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name')->unique();
            $table->string('genus')->nullable();
            $table->decimal('height', 5, 2)->default(0);
            $table->decimal('weight', 5, 2)->default(0);
            $table->string('sprite')->nullable();
            $table->json('types');
            $table->text('description')->nullable();
            $table->integer('hp')->default(0);
            $table->integer('attack')->default(0);
            $table->integer('defense')->default(0);
            $table->integer('special_attack')->default(0);
            $table->integer('special_defense')->default(0);
            $table->integer('speed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokemon');
    }
};
