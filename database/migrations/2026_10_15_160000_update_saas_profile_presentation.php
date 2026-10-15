<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Target only the original presentation; preserve body, status, dates and later editorial changes.
        DB::table('articles')->where('slug', 'esperto-technologies-saas-development-india')
            ->where('title', 'Esperto Technologies: SaaS development in India, from MVP to scale')
            ->update(['title'=>'SaaS Development in India: From MVP to a Scalable Platform', 'updated_at'=>now()->format('Y-m-d H:i:s')]);
        DB::table('articles')->where('slug', 'esperto-technologies-saas-development-india')
            ->where('image', '/assets/articles/esperto-technologies-saas-development-india.jpg')
            ->update(['image'=>'/assets/articles/esperto-technologies-saas-development-india-v2.jpg', 'updated_at'=>now()->format('Y-m-d H:i:s')]);
    }

    public function down(): void
    {
        // Editorial changes are not reverted by schema rollbacks.
    }
};
