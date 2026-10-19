<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FundingStartup extends Model {
    public const SECTORS = ['Payments','Infrastructure','Security','DeFi','AI & Data','RWA','Gaming','Other'];
    protected $fillable = ['name','slug','sector','description','website','project_id'];
    public function rounds() { return $this->hasMany(FundingRound::class); }
    public function project() { return $this->belongsTo(Project::class); }
}
