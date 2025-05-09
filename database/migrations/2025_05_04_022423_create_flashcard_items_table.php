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
        Schema::create('flashcard_items', function (Blueprint $table) {
            $table->id();
            $table->integer('flashcard_id')->unsigned();
            $table->text('content')->nullable();
            $table->text('corrected_content')->nullable();
            $table->text('feedback')->nullable();
            
            $table->enum('level', ['very_easy', 'easy', 'medium', 'hard', 'very_hard'])->default('easy');

            // $table->timestamp('started_at')->nullable();
            // $table->timestamp('answered_at')->nullable();

            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        Schema::table('flashcard_items', function ($table) {
            $table->foreign('flashcard_id')->references('id')->on('flashcards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flashcard_items');
    }
};
