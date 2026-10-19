<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FundingSubmission extends Model {
    protected $fillable = ['name','email','startup_name','website','announcement_url','message','consented_at','status','notes'];
    protected function casts(): array { return ['consented_at'=>'datetime']; }
}
