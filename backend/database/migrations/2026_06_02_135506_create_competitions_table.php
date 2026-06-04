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
        Schema::create('competitions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('competition_category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->string('slug')->unique();

            $table->longText('description');

            $table->longText('rules')->nullable();

            $table->string('banner')->nullable();

            $table->decimal('entry_fee', 10, 2)->default(0);

            $table->integer('max_participants')->nullable();

            $table->integer('min_age')->nullable();

            $table->integer('max_age')->nullable();

            $table->enum('gender', [
                'all',
                'male',
                'female'
            ])->default('all');

            $table->string('country')->default('India');

            $table->string('state')->nullable();

            $table->string('district')->nullable();

            $table->string('city')->nullable();

            $table->enum('submission_type', [
                'image',
                'video',
                'audio',
                'document'
            ]);

            $table->tinyInteger('total_rounds')->default(1);

            $table->dateTime('registration_start');

            $table->dateTime('registration_end');

            $table->dateTime('competition_start');

            $table->dateTime('competition_end');

            $table->boolean('allow_public_voting')->default(false);

            $table->boolean('show_scores')->default(false);

            $table->boolean('certificate_enabled')->default(true);

            $table->enum('status', [
                'draft',
                'published',
                'completed',
                'cancelled'
            ])->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
