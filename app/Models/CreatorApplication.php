<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreatorApplication extends Model
{
    public const PLATFORMS = ['YouTube','X','Instagram','Telegram','TikTok','LinkedIn','Newsletter','Podcast','Other'];
    public const TOPICS = ['Bitcoin','Ethereum','DeFi','Web3 & infrastructure','Trading & market analysis','AI & crypto','Crypto education','Gaming & NFTs'];
    public const FORMATS = ['Sponsored videos','Short-form content','Sponsored posts & threads','Community campaigns','Podcasts & interviews','Events & AMAs','Long-term ambassadorships'];
    public const STATUSES = ['new'=>'New','reviewing'=>'Reviewing','contacted'=>'Contacted','approved'=>'Approved','not_a_fit'=>'Not a fit'];
    protected $fillable = ['name','email','phone','platform','profile_url','followers','location','languages','topics','formats','rate','message','fingerprint','consented_at','status','notes'];
    protected function casts(): array { return ['topics'=>'array','formats'=>'array','followers'=>'integer','consented_at'=>'datetime']; }
}
