<?php
namespace App\Http\Controllers;
use App\Models\{FundingRound,FundingStartup,FundingSubmission};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class FundingController extends Controller {
    public function index(Request $request) {
        $filters=$request->validate(['q'=>['nullable','string','max:120'],'year'=>['nullable','integer','in:2025,2026'],'sector'=>['nullable',Rule::in(FundingStartup::SECTORS)],'stage'=>['nullable',Rule::in(FundingRound::STAGES)],'type'=>['nullable',Rule::in(FundingRound::TYPES)],'investor'=>['nullable','string','max:120']]);
        $query=FundingRound::published();
        if ($q=trim($filters['q'] ?? '')) $query->whereHas('startup',fn($s)=>$s->where('name','like','%'.$q.'%')->orWhere('description','like','%'.$q.'%'));
        if ($filters['year'] ?? '') $query->whereYear('announced_on',$filters['year']);
        if ($filters['sector'] ?? '') $query->whereHas('startup',fn($s)=>$s->where('sector',$filters['sector']));
        foreach (['stage','type'] as $field) if ($filters[$field] ?? '') $query->where($field,$filters[$field]);
        if ($investor=trim($filters['investor'] ?? '')) $query->where(fn($q)=>$q->where('lead_investors','like','%'.$investor.'%')->orWhere('other_investors','like','%'.$investor.'%'));
        $stats=['rounds'=>(clone $query)->count(),'startups'=>(clone $query)->distinct()->count('funding_startup_id'),'disclosed'=>(clone $query)->whereNotNull('amount_usd')->count(),'venture'=>(clone $query)->where('type','Venture')->sum('amount_usd'),'other'=>(clone $query)->where('type','!=','Venture')->sum('amount_usd')];
        return view('funding.index',[...$this->social(),'title'=>'Crypto Funding Tracker — Startup Rounds & Investors','description'=>'Explore sourced crypto startup funding announcements from 2025 and 2026. Filter rounds by stage, sector, investor and financing type.','filters'=>$filters,'stats'=>$stats,'rounds'=>$query->with('startup')->orderByDesc('announced_on')->orderByDesc('id')->paginate(12)->withQueryString(),'lastChecked'=>FundingRound::published()->max('checked_at')]);
    }
    public function show(string $slug) {
        $round=FundingRound::published()->with('startup.project')->where('slug',$slug)->firstOrFail();
        $name=$round->startup->name; $url=rtrim(config('app.url'),'/').'/funding/'.$slug;
        $schema=['@context'=>'https://schema.org','@graph'=>[
            ['@type'=>'WebPage','@id'=>$url,'url'=>$url,'name'=>$name.' — '.$round->stage.' funding','description'=>$round->summary,'dateModified'=>$round->updated_at->toAtomString(),'citation'=>array_values(array_filter(preg_split('/\R/',$round->sources),'safe_url'))],
            ['@type'=>'BreadcrumbList','itemListElement'=>[['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>rtrim(config('app.url'),'/').'/'],['@type'=>'ListItem','position'=>2,'name'=>'Crypto Funding','item'=>rtrim(config('app.url'),'/').'/funding'],['@type'=>'ListItem','position'=>3,'name'=>$name.' '.$round->stage,'item'=>$url]]],
        ]];
        return view('funding.show',[...$this->social(),'round'=>$round,'title'=>$name.' — '.$round->stage.' Funding | '.FundingRound::money($round->amount_usd),'description'=>mb_substr($round->summary,0,180),'pageSchema'=>$schema,'history'=>FundingRound::published()->where('funding_startup_id',$round->funding_startup_id)->orderByDesc('announced_on')->get()]);
    }
    private function social(): array {
        return ['socialPreview'=>['url'=>rtrim(config('app.url'),'/').'/assets/social/funding-v1.jpg','type'=>'image/jpeg','width'=>1200,'height'=>630],'socialImageAlt'=>'Compyvila Crypto Funding — startup rounds, investors and original sources for 2025–2026 announcements.'];
    }
    public function submit(Request $request) {
        $data=$request->validate(['name'=>['required','string','max:120'],'email'=>['required','email','max:190'],'startup_name'=>['required','string','max:120'],'website'=>['required','url:http,https','max:500'],'announcement_url'=>['required','url:http,https','max:1000'],'message'=>['nullable','string','max:3000'],'consent'=>['accepted'],'company_fax'=>['nullable','string','max:0']]);
        unset($data['consent'],$data['company_fax']); $data['message']=$data['message'] ?? ''; $data['email']=strtolower($data['email']);
        $data['consented_at']=now(); FundingSubmission::create($data);
        return redirect('/funding#submit')->with('flash','Funding announcement received. It will be reviewed before any listing is published.');
    }
}
