<?php
namespace App\Support;
use App\Models\{FundingRound,FundingStartup,Project};
use Illuminate\Support\Facades\{DB,Validator};
use Illuminate\Validation\Rule;
class FundingPublisher {
    public static function publish(bool $write): array {
        $data=json_decode(file_get_contents(base_path('content/funding-2025-2026.json')),true,flags:JSON_THROW_ON_ERROR);
        foreach($data['startups'] as $s) Validator::make($s,['slug'=>['required','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/','distinct'],'name'=>['required','max:120'],'sector'=>['required',Rule::in(FundingStartup::SECTORS)],'description'=>['required','min:20','max:5000'],'website'=>['nullable','url:http,https','max:500']])->validate();
        $slugs=array_column($data['startups'],'slug');$roundSlugs=[];
        if(count($slugs)!==count(array_unique($slugs)))throw new \RuntimeException('Duplicate startup slugs.');
        foreach($data['rounds'] as $r){
            Validator::make($r,['startup_slug'=>['required',Rule::in($slugs)],'slug'=>['required','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/','max:190'],'announced_on'=>['required','date_format:Y-m-d','after_or_equal:2025-01-01','before_or_equal:today'],'stage'=>['required',Rule::in(FundingRound::STAGES)],'type'=>['required',Rule::in(FundingRound::TYPES)],'amount_usd'=>['nullable','numeric','gt:0','max:999999999999999.99'],'valuation_usd'=>['nullable','numeric','gt:0','max:999999999999999.99'],'valuation_basis'=>['required',Rule::in(FundingRound::VALUATIONS)],'summary'=>['required','min:30','max:5000'],'sources'=>['required','array','min:1'],'sources.*'=>['required','url:http,https'],'lead_investors'=>['present','array'],'other_investors'=>['present','array'],'source_notes'=>['present','string']])->validate();
            if(($r['valuation_usd']===null)!==($r['valuation_basis']==='Undisclosed'))throw new \RuntimeException('Inconsistent valuation.');
            $roundSlugs[]=$r['slug'];
        }
        if(count($roundSlugs)!==count(array_unique($roundSlugs)))throw new \RuntimeException('Duplicate round slugs.');
        if(!$write)return ['validated'=>count($data['rounds']),'created'=>0];
        return DB::transaction(function()use($data){$created=0;
            foreach($data['startups'] as $s)FundingStartup::firstOrCreate(['slug'=>$s['slug']],array_merge($s,['project_id'=>$s['slug']==='walrus'?Project::where('slug','walrus')->value('id'):null]));
            foreach($data['rounds'] as $r){$startup=FundingStartup::where('slug',$r['startup_slug'])->firstOrFail();unset($r['startup_slug']);foreach(['sources','lead_investors','other_investors']as $f)$r[$f]=implode("\n",$r[$f]);$record=FundingRound::firstOrCreate(['slug'=>$r['slug']],array_merge($r,['funding_startup_id'=>$startup->id,'notes'=>'','checked_at'=>now(),'status'=>'published','published_at'=>now()]));if($record->wasRecentlyCreated)$created++;}
            return ['validated'=>count($data['rounds']),'created'=>$created];
        });
    }
}
