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
        Schema::create('winner_change_logs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('competition_id');

            $table->foreignId('old_participant_id');

            $table->foreignId('new_participant_id');

            $table->foreignId('changed_by');

            $table->text('reason');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('winner_change_logs');
    }
};
