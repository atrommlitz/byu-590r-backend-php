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
            //PK's
            $table->id();
            //FK's
            // $table->integer('director_id');
            // $table->integer('language_id');
            // $table->integer('age_rating_id');
            $table->string('title');
            $table->integer('year');
            $table->string('genre');
            $table->string('file');
            $table->float('movie_length');
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
