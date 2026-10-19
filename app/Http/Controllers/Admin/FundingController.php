<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{FundingRound,FundingStartup,FundingSubmission,Project};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class FundingController extends Controller {
    public function index(Request $r) {
        $q=trim(mb_substr((string)$r->query('q',''),0,120));
        $rounds=FundingRound::with('startup')->when($q,fn($query)=>$query->whereHas('startup',fn($s)=>$s->where('name','like','%'.$q.'%')))->orderByDesc('announced_on')->paginate(25)->withQueryString();
        return view('admin.funding.index',['rounds'=>$rounds,'q'=>$q,'startups'=>FundingStartup::orderBy('name')->get(),'submissions'=>FundingSubmission::latest()->paginate(15,['*'],'leads_page'),'newLeads'=>FundingSubmission::where('status','new')->count()]);
    }
    public function edit(Request $r) { return view('admin.funding.round',['edit'=>$r->filled('id')?FundingRound::findOrFail($r->integer('id'))->toArray():[],'startups'=>FundingStartup::orderBy('name')->get()]); }
    public function save(Request $r) {
        $round=$r->integer('id')?FundingRound::findOrFail($r->integer('id')):new FundingRound;
        $r->merge(['slug'=>Str::slug($r->input('slug',''))]);
        $data=$r->validate([
            'funding_startup_id'=>['required','integer','exists:funding_startups,id'],'slug'=>['required','string','max:190',Rule::unique('funding_rounds','slug')->ignore($round->id)],
            'announced_on'=>['required','date_format:Y-m-d','after_or_equal:2025-01-01','before_or_equal:2026-12-31',Rule::when($r->input('status')==='published','before_or_equal:today')],
            'stage'=>['required',Rule::in(FundingRound::STAGES)],'type'=>['required',Rule::in(FundingRound::TYPES)],
            'amount_usd'=>['nullable','numeric','gt:0','max:999999999999999.99','decimal:0,2'],'valuation_usd'=>['nullable','numeric','gt:0','max:999999999999999.99','decimal:0,2'],
            'valuation_basis'=>['required',Rule::in(FundingRound::VALUATIONS),function($a,$v,$fail)use($r){if(($r->filled('valuation_usd')&&$v==='Undisclosed')||(!$r->filled('valuation_usd')&&$v!=='Undisclosed'))$fail('Use Undisclosed only when the valuation amount is blank.');}],
            'lead_investors'=>['nullable','string','max:5000'],'other_investors'=>['nullable','string','max:10000'],'summary'=>['required','string','min:30','max:5000'],
            'source_notes'=>['nullable','string','max:5000'],'notes'=>['nullable','string','max:10000'],'status'=>['required',Rule::in(['draft','published'])],
            'reviewed'=>$r->input('status')==='published'?['required','accepted']:['sometimes','accepted'],
            'sources'=>['required_if:status,published','nullable','string','max:10000',function($a,$v,$fail)use($r){$lines=array_filter(array_map('trim',preg_split('/\R/',$v ?? '')));if($r->input('status')==='published'&&!$lines)$fail('Add at least one original source.');foreach($lines as $line)if(!safe_url($line)){$fail('Use complete HTTP or HTTPS source URLs, one per line.');break;}}],
        ]);
        unset($data['reviewed']);foreach(['lead_investors','other_investors','sources','source_notes','notes'] as $f)$data[$f]=trim($data[$f]??'');
        $data['checked_at']=$r->boolean('reviewed')?now():$round->checked_at;
        $data['published_at']=$round->published_at ?? ($data['status']==='published'?now():null);
        $round->fill($data)->save();return redirect('/admin/funding')->with('flash','Funding round saved.');
    }
    public function startupEdit(Request $r) { return view('admin.funding.startup',['edit'=>$r->filled('id')?FundingStartup::findOrFail($r->integer('id'))->toArray():[],'projects'=>Project::orderBy('name')->get()]); }
    public function startupSave(Request $r) {
        $startup=$r->integer('id')?FundingStartup::findOrFail($r->integer('id')):new FundingStartup;
        $r->merge(['slug'=>Str::slug($r->input('slug')?:$r->input('name',''))]);
        $data=$r->validate(['name'=>['required','string','max:120'],'slug'=>['required','string','max:190',Rule::unique('funding_startups','slug')->ignore($startup->id)],'sector'=>['required',Rule::in(FundingStartup::SECTORS)],'description'=>['required','string','min:20','max:5000'],'website'=>['nullable','url:http,https','max:500'],'project_id'=>['nullable','integer','exists:projects,id']]);
        $data['website']=$data['website']??'';$startup->fill($data)->save();return redirect('/admin/funding')->with('flash','Startup saved. You can now add its funding rounds.');
    }
    public function destroy(Request $r,FundingRound $round) { $r->validate(['confirm'=>['accepted']]);$round->delete();return redirect('/admin/funding')->with('flash','Funding round deleted.'); }
    public function submission(Request $r,FundingSubmission $submission) {
        if($r->isMethod('get'))return view('admin.funding.submission',['submission'=>$submission]);
        $data=$r->validate(['status'=>['required',Rule::in(['new','reviewed','declined'])],'notes'=>['nullable','string','max:10000']]);$submission->update($data);return back()->with('flash','Submission updated. Publication is managed separately.');
    }
    public function deleteSubmission(Request $r,FundingSubmission $submission) { $r->validate(['confirm'=>['accepted']]);$submission->delete();return redirect('/admin/funding')->with('flash','Submission deleted.'); }
}
