<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SebastianBergmann\Type\VoidType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::dropIfExists(table: 'movies');
            Schema::create(table: 'movies', callback: function(Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->integer('year');
                $table->string('genre');
                $table->string('file')->nullable();
                $table->float('movie_length');
                $table->foreignId('director_id')->nullable()->constrained('directors')->onDelete('set null');
                $table->foreignId('rating_id')->nullable()->constrained('ratings')->onDelete('set null');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
