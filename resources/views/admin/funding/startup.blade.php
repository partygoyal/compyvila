@extends('admin.layout')
@section('content')
@php($edit=array_merge($edit,session()->getOldInput()))
<h1>{{ empty($edit['id']) ? 'Add a startup.' : 'Edit startup profile.' }}</h1><p class="intro">Use one profile per company. Keep individual round amounts in the funding-round form.</p><form class="admin-form" method="post" action="/admin/funding/startup">@csrf<input type="hidden" name="id" value="{{ (int)($edit['id'] ?? 0) }}">@foreach(['name'=>'Startup name','slug'=>'Slug (auto-generated when blank)','website'=>'Official website (optional)'] as $name=>$label)@include('admin.fields.input',['name'=>$name,'label'=>$label,'value'=>$edit[$name] ?? '', 'type'=>$name==='website'?'url':'text','required'=>$name==='name'])@endforeach
@include('admin.fields.select',['name'=>'sector','label'=>'Sector','options'=>\App\Models\FundingStartup::SECTORS,'value'=>$edit['sector'] ?? 'Infrastructure'])
@include('admin.fields.textarea',['name'=>'description','label'=>'What the startup builds (plain text; 20–5000 characters)','value'=>$edit['description'] ?? '', 'class'=>''])
<label>Crypto Radar profile (optional)<select name="project_id"><option value="">No linked project</option>@foreach($projects as $project)<option value="{{ $project->id }}" @selected(($edit['project_id'] ?? '')==$project->id)>{{ $project->name }} ({{ $project->status }})</option>@endforeach</select></label><div class="button-row"><button class="button">Save startup ↗</button><a href="/admin/funding">Cancel</a></div></form>
@endsection
