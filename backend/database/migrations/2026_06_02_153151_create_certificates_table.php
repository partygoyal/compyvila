<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {

            $table->id();

            $table->foreignId('competition_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('winner_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('certificate_number')
                ->unique();

            $table->enum('type', [
                'winner',
                'runner_up',
                'participation'
            ]);

            $table->string('pdf_path')
                ->nullable();

            $table->timestamp('issued_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};