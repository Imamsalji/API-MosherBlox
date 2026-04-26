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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Polymorphic: bisa ke App, Product, dll
            $table->morphs('ratable'); // → ratable_id, ratable_type

            $table->tinyInteger('score')->unsigned(); // 1-5
            $table->string('title', 100)->nullable();
            $table->text('body')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamps();
            $table->softDeletes();

            // Satu user hanya bisa rating 1x per item
            $table->unique(['user_id', 'ratable_id', 'ratable_type']);

            $table->index(['ratable_id', 'ratable_type', 'status']); // untuk aggregasi cepat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
