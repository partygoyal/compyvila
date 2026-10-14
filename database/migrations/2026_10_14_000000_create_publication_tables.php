<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adopt the existing publication tables without deleting or rewriting records.
        $definitions = [
            'settings' => function (Blueprint $table) {
                $table->string('name', 100)->primary();
                $table->text('value');
            },
            'admins' => function (Blueprint $table) {
                $table->id();
                $table->string('email', 190)->unique();
                $table->string('password');
            },
            'articles' => function (Blueprint $table) {
                $table->id();
                $table->string('slug', 190)->unique();
                $table->string('title');
                $table->text('excerpt');
                $table->longText('body');
                $table->string('category', 50);
                $table->string('coin', 50)->default('');
                $table->string('image')->default('');
                $table->text('sources');
                $table->string('status', 20)->default('draft');
                $table->boolean('featured')->default(false);
                $table->string('published_at', 30);
                $table->string('updated_at', 30);
                $table->index(['status', 'published_at']);
            },
            'snapshots' => function (Blueprint $table) {
                $table->id();
                $table->string('symbol', 12)->unique();
                $table->string('name', 80);
                $table->decimal('price', 20, 6);
                $table->decimal('change_pct', 10, 2);
                $table->string('captured_at', 30);
                $table->string('source')->default('');
            },
            'events' => function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->string('event_at', 30)->index();
                $table->string('category', 50);
                $table->string('source')->default('');
                $table->string('verified_at', 30);
            },
            'subscribers' => function (Blueprint $table) {
                $table->id();
                $table->string('email', 190)->unique();
                $table->string('created_at', 30);
            },
            'enquiries' => function (Blueprint $table) {
                $table->id();
                $table->string('name', 120);
                $table->string('email', 190);
                $table->string('company', 190);
                $table->string('placement', 100);
                $table->text('message');
                $table->string('created_at', 30);
            },
        ];

        foreach ($definitions as $name => $definition) {
            if (! Schema::hasTable($name)) {
                Schema::create($name, $definition);
            }
        }
    }

    public function down(): void
    {
        throw new RuntimeException('This baseline adopts existing publication data and cannot be rolled back safely. Restore a database backup instead.');
    }
};
