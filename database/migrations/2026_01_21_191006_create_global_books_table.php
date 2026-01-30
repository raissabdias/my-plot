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
        Schema::create('global_books', function (Blueprint $table) {
            $table->id();
            $table->string('google_book_id')->unique()->index();
            $table->string('title');
            $table->string('authors')->nullable();
            $table->text('description')->nullable();
            $table->text('cover_url')->nullable();
            $table->string('isbn')->nullable();
            $table->integer('page_count')->nullable();
            $table->string('published_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_books');
    }
};
