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
        Schema::create('targets', function (Blueprint $table) {
            $table->id();

            $table->text('name', 64);
            $table->float('radeg', 12, 8);
            $table->float('decdeg', 12, 8);
            $table->float('Vmag', 5, 2)->nullable();

            $table->json('orbital_elements', 6400)->nullable();
            $table->text('notes', 6400)->nullable();

            /*
             One to one relation with user
             Each target belongs to a single user
             With cascade, if the user is removed, the target is removed
            */
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
