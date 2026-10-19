<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('funding_startups', function (Blueprint $t) {
            $t->id(); $t->string('name',120); $t->string('slug',190)->unique(); $t->string('sector',50);
            $t->text('description'); $t->string('website',500)->default('');
            $t->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete(); $t->timestamps();
        });
        Schema::create('funding_rounds', function (Blueprint $t) {
            $t->id(); $t->foreignId('funding_startup_id')->constrained()->restrictOnDelete(); $t->string('slug',190)->unique();
            $t->date('announced_on'); $t->string('stage',50); $t->string('type',50)->default('Venture');
            $t->decimal('amount_usd',20,2)->nullable(); $t->decimal('valuation_usd',20,2)->nullable();
            $t->string('valuation_basis',40)->default('Undisclosed'); $t->text('lead_investors'); $t->text('other_investors');
            $t->text('summary'); $t->text('sources'); $t->text('source_notes'); $t->text('notes');
            $t->timestamp('checked_at')->nullable(); $t->string('status',20)->default('draft'); $t->timestamp('published_at')->nullable(); $t->timestamps();
            $t->index(['status','announced_on']); $t->index(['type','stage']);
        });
        Schema::create('funding_submissions', function (Blueprint $t) {
            $t->id(); $t->string('name',120); $t->string('email',190); $t->string('startup_name',120);
            $t->string('website',500); $t->string('announcement_url',1000); $t->text('message');
            $t->timestamp('consented_at'); $t->string('status',20)->default('new'); $t->text('notes')->nullable(); $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('funding_submissions'); Schema::dropIfExists('funding_rounds'); Schema::dropIfExists('funding_startups');
    }
};
