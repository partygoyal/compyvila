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
        Schema::create('user_profiles', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('mobile')->nullable();

            $table->date('date_of_birth')->nullable();

            $table->integer('age')->nullable();

            $table->string('gender')->nullable();

            $table->string('country')->nullable();

            $table->string('state')->nullable();

            $table->string('city')->nullable();

            $table->text('address')->nullable();

            $table->text('bio')->nullable();

            $table->string('instagram')->nullable();

            $table->string('facebook')->nullable();

            $table->string('youtube')->nullable();

            $table->string('website')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
