@extends('admin.layout')
@section('content')
@php $edit = array_merge($edit, session()->getOldInput()); @endphp
<h1>{{ empty($edit['id']) ? 'Add a project worth researching.' : 'Edit project listing.' }}</h1>
<p class="intro">The year identifies a specific launch milestone, not the year added to this directory. Use original sources and distinguish plans from completed launches.</p>
<form method="post" action="/admin/project" class="admin-form">@csrf
    <input type="hidden" name="id" value="{{ (int)($edit['id'] ?? 0) }}">
    @include('admin.fields.input', ['name'=>'name','label'=>'Project name','value'=>$edit['name'] ?? '', 'type'=>'text','required'=>true])
    @include('admin.fields.input', ['name'=>'slug','label'=>'URL slug (auto-generated when blank)','value'=>$edit['slug'] ?? '', 'type'=>'text','required'=>false])
    @include('admin.fields.input', ['name'=>'tagline','label'=>'Short description (10–300 characters)','value'=>$edit['tagline'] ?? '', 'type'=>'text','required'=>true])
    @include('admin.fields.textarea', ['name'=>'description','label'=>'Project overview, milestones, and risks (Markdown supported)','value'=>$edit['description'] ?? '', 'class'=>'body-field'])
    <div class="form-columns">
    @include('admin.fields.select', ['name'=>'category','label'=>'Category','options'=>\App\Models\Project::CATEGORIES,'value'=>$edit['category'] ?? 'Infrastructure'])
    @include('admin.fields.select', ['name'=>'stage','label'=>'Current stage','options'=>\App\Models\Project::STAGES,'value'=>$edit['stage'] ?? 'Upcoming'])
    </div><div class="form-columns">
    @include('admin.fields.input', ['name'=>'network','label'=>'Network / ecosystem (optional)','value'=>$edit['network'] ?? '', 'type'=>'text','required'=>false])
    @include('admin.fields.input', ['name'=>'symbol','label'=>'Token symbol (optional; leave blank if none)','value'=>$edit['symbol'] ?? '', 'type'=>'text','required'=>false])
    </div><div class="form-columns">
    @include('admin.fields.select', ['name'=>'launch_year','label'=>'Launch milestone year','options'=>['2025','2026'],'value'=>(string)($edit['launch_year'] ?? '2026')])
    @include('admin.fields.select', ['name'=>'milestone','label'=>'Which milestone does this year represent?','options'=>\App\Models\Project::MILESTONES,'value'=>$edit['milestone'] ?? 'Mainnet launch'])
    </div>
    @include('admin.fields.input', ['name'=>'launch_date','label'=>'Milestone date (optional; must match year)','value'=>empty($edit['launch_date']) ? '' : substr($edit['launch_date'],0,10), 'type'=>'date','required'=>false])
    @include('admin.fields.input', ['name'=>'website','label'=>'Official project website','value'=>$edit['website'] ?? '', 'type'=>'url','required'=>true])
    @include('admin.fields.textarea', ['name'=>'sources','label'=>'Primary source URLs for the milestone and claims (one per line; required to publish)','value'=>$edit['sources'] ?? '', 'class'=>''])
    @include('admin.fields.select', ['name'=>'status','label'=>'Publication status','options'=>['draft','published'],'value'=>$edit['status'] ?? 'draft'])
    <p class="intro muted">Saving updates the public review date. Check source links before publishing. Drafts stay private.</p>
    <div class="button-row"><button class="button">Save project ↗</button><a class="button outline" href="/admin?tab=projects">Cancel</a></div>
</form>
@endsection
