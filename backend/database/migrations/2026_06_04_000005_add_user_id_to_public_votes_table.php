<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_votes', function (Blueprint $table) {
            $table->dropUnique([
                'submission_id',
                'voter_hash',
            ]);

            $table->foreignId('user_id')
                ->nullable()
                ->after('submission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unique([
                'submission_id',
                'user_id',
            ]);
        });

        DB::table('public_votes')
            ->whereNull('user_id')
            ->delete();
    }

    public function down(): void
    {
        Schema::table('public_votes', function (Blueprint $table) {
            $table->dropUnique([
                'submission_id',
                'user_id',
            ]);

            $table->dropConstrainedForeignId('user_id');

            $table->unique([
                'submission_id',
                'voter_hash',
            ]);
        });
    }
};
