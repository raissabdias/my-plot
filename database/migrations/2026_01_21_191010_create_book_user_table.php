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
        Schema::create('book_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('global_book_id')->constrained('global_books')->onDelete('cascade');
            $table->string('status')->default('WANT_TO_READ');
            $table->text('review')->nullable();
            $table->integer('rating')->nullable();
            $table->date('started_at')->nullable();
            $table->date('finished_at')->nullable();
            $table->unique(['user_id', 'global_book_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_user');
    }
};
