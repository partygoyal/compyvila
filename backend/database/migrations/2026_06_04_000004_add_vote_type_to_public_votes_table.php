<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_votes', function (Blueprint $table) {
            $table->index('competition_id');

            $table->dropUnique([
                'competition_id',
                'voter_hash',
            ]);

            $table->enum('vote_type', [
                'up',
                'down',
            ])
                ->default('up')
                ->after('voter_hash');

            $table->unique([
                'submission_id',
                'voter_hash',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('public_votes', function (Blueprint $table) {
            $table->dropUnique([
                'submission_id',
                'voter_hash',
            ]);

            $table->dropColumn('vote_type');

            $table->unique([
                'competition_id',
                'voter_hash',
            ]);

            $table->dropIndex([
                'competition_id',
            ]);
        });
    }
};
