<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->date('dob')->nullable();

            $table->enum('gender', [
                'male',
                'female',
                'other'
            ])->nullable();

            $table->text('address')->nullable();

            $table->string('country')->default('India');

            $table->string('state')->nullable();

            $table->string('district')->nullable();

            $table->string('city')->nullable();

            $table->string('pincode', 20)->nullable();

            $table->string('profile_photo')->nullable();

            // Competition-specific fields
            $table->string('mobile')->nullable();
            $table->string('occupation')->nullable();
            $table->string('school_college')->nullable();

            // Social Links
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('website_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};