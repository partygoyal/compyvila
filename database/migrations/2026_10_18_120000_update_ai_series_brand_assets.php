<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        foreach (['ai-agents-crypto-wallets','bittensor-decentralized-ai','akash-ai-gpu-compute'] as $slug) {
            DB::table('articles')->where('slug',$slug)->where('image','/assets/articles/'.$slug.'.webp')
                ->update(['image'=>'/assets/articles/'.$slug.'-v3.webp','updated_at'=>now()->format('Y-m-d H:i:s')]);
        }
    }
    public function down(): void { /* Preserve editorial presentation changes. */ }
};
