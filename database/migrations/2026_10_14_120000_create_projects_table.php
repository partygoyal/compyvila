<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 190)->unique();
            $table->string('tagline', 300);
            $table->text('description');
            $table->string('category', 50);
            $table->string('network', 120)->default('');
            $table->string('symbol', 20)->default('');
            $table->unsignedSmallInteger('launch_year');
            $table->string('milestone', 50);
            $table->date('launch_date')->nullable();
            $table->string('stage', 30);
            $table->string('website', 500);
            $table->text('sources');
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'launch_year', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
