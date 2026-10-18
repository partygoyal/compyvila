<?php
namespace App\Http\Controllers;

use App\Models\CreatorApplication;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\UniqueConstraintViolationException;

class CreatorController extends Controller
{
    public function index()
    {
        return view('creators', ['title'=>'Join the Compyvila Creator Network','description'=>'Join Compyvila’s network for crypto creators. Apply for paid brand collaborations, campaign opportunities, and long-term partnerships.', 'path'=>'/creator-network', 'socialPreview'=>['url'=>rtrim(config('app.url'),'/').'/assets/social/creator-network-v2.jpg','type'=>'image/jpeg','width'=>1200,'height'=>630], 'socialImageAlt'=>'Compyvila Creator Network — Your voice. Bigger possibilities.']);
    }
    public function store(Request $request)
    {
        $request->merge(['email'=>strtolower(trim((string)$request->input('email')))]);
        $data=$request->validate([
            'name'=>['required','string','max:120'], 'email'=>['required','email','max:190'],
            'phone'=>['nullable','string','max:40'], 'platform'=>['required',Rule::in(CreatorApplication::PLATFORMS)],
            'profile_url'=>['required','url:http,https','max:500'], 'followers'=>['required','integer','min:0','max:10000000000'],
            'location'=>['required','string','max:120'], 'languages'=>['required','string','max:190'],
            'topics'=>['required','array','min:1','max:8'], 'topics.*'=>['required','distinct',Rule::in(CreatorApplication::TOPICS)],
            'formats'=>['required','array','min:1','max:7'], 'formats.*'=>['required','distinct',Rule::in(CreatorApplication::FORMATS)],
            'rate'=>['nullable','string','max:190'], 'message'=>['nullable','string','max:3000'],
            'consent'=>['accepted'], 'website'=>['nullable','string','max:0'],
        ]);
        unset($data['consent'],$data['website']);
        $data['fingerprint']=hash('sha256',$data['email'].'|'.rtrim($data['profile_url'],'/'));
        $data['consented_at']=now();
        try { CreatorApplication::create($data); }
        catch (UniqueConstraintViolationException $error) {
            return redirect('/creator-network#apply')->withErrors(['email'=>'An application with this email and profile is already saved. Contact Compyvila if you need to update it.'])->withInput();
        }
        return redirect('/creator-network#apply')->with('creator_success',true)->with('flash','Your creator application is saved. Compyvila will review your profile and contact you about suitable opportunities.');
    }
}
