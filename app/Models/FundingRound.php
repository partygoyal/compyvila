<?php
namespace App\Models;
use Illuminate\Database\Eloquent\{Builder, Model};
class FundingRound extends Model {
    public const STAGES = ['Pre-seed','Seed','Series A','Series B','Series C','Series D+','Strategic','Undisclosed'];
    public const TYPES = ['Venture','Private token sale','Public token sale','Grant','Debt'];
    public const VALUATIONS = ['Undisclosed','Pre-money','Post-money','Token FDV','Unspecified'];
    protected $fillable = ['funding_startup_id','slug','announced_on','stage','type','amount_usd','valuation_usd','valuation_basis','lead_investors','other_investors','summary','sources','source_notes','notes','checked_at','status','published_at'];
    protected function casts(): array { return ['announced_on'=>'date','amount_usd'=>'decimal:2','valuation_usd'=>'decimal:2','checked_at'=>'datetime','published_at'=>'datetime']; }
    public function startup() { return $this->belongsTo(FundingStartup::class,'funding_startup_id'); }
    public function scopePublished(Builder $q): Builder { return $q->where('status','published')->where('published_at','<=',now())->where('announced_on','<=',today()->toDateString()); }
    public static function money($value): string {
        if ($value === null) return 'Undisclosed';
        $n=(float)$value;
        foreach ([1e9=>'B',1e6=>'M',1e3=>'K'] as $d=>$suffix) if ($n >= $d) return '$'.rtrim(rtrim(number_format($n/$d,3,'.',''),'0'),'.').$suffix;
        return '$'.number_format($n,2);
    }
}
