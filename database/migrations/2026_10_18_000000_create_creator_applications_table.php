<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('creator_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name',120);
            $table->string('email',190)->index();
            $table->string('phone',40)->nullable();
            $table->string('platform',40);
            $table->string('profile_url',500);
            $table->unsignedBigInteger('followers');
            $table->string('location',120);
            $table->string('languages',190);
            $table->json('topics');
            $table->json('formats');
            $table->string('rate',190)->nullable();
            $table->text('message')->nullable();
            $table->string('fingerprint',64)->unique();
            $table->timestamp('consented_at');
            $table->string('status',30)->default('new')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('creator_applications'); }
};
