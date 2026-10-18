<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreatorApplication;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CreatorController extends Controller
{
    public function index(Request $request)
    {
        $data=$request->validate(['q'=>['nullable','string','max:190'],'status'=>['nullable',Rule::in(array_keys(CreatorApplication::STATUSES))]]);
        $query=CreatorApplication::query();
        if ($data['status'] ?? '') $query->where('status',$data['status']);
        if ($q=trim($data['q'] ?? '')) $query->where(fn($query)=>$query->where('name','like','%'.$q.'%')->orWhere('email','like','%'.$q.'%')->orWhere('platform','like','%'.$q.'%'));
        return view('admin.creators',['tab'=>'creators','applications'=>$query->latest('created_at')->orderByDesc('id')->paginate(20)->withQueryString(),'q'=>$q,'status'=>$data['status'] ?? '','total'=>CreatorApplication::count(),'newCount'=>CreatorApplication::where('status','new')->count()]);
    }
    public function show(CreatorApplication $creator)
    {
        return view('admin.creator',['tab'=>'creators','creator'=>$creator]);
    }
    public function update(Request $request, CreatorApplication $creator)
    {
        $data=$request->validate(['status'=>['required',Rule::in(array_keys(CreatorApplication::STATUSES))],'notes'=>['nullable','string','max:10000']]);
        $creator->update($data);
        return back()->with('flash','Creator lead updated.');
    }
    public function destroy(CreatorApplication $creator)
    {
        $creator->delete();
        return redirect('/admin/creators')->with('flash','Creator application deleted.');
    }
}
