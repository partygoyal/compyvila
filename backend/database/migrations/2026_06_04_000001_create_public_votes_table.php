<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_votes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('competition_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('submission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('voter_hash');

            $table->timestamps();

            $table->unique([
                'competition_id',
                'voter_hash',
            ]);

            $table->index([
                'submission_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_votes');
    }
};
