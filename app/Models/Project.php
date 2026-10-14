<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public const YEARS = [2025, 2026];
    public const CATEGORIES = ['DeFi', 'Layer 1', 'Layer 2', 'Infrastructure', 'AI & Data', 'Gaming', 'Payments', 'Other'];
    public const STAGES = ['Upcoming', 'Testnet', 'Live', 'Paused'];
    public const MILESTONES = ['Project announcement', 'Testnet launch', 'Mainnet launch', 'Token launch', 'Product launch'];

    protected $fillable = ['name', 'slug', 'tagline', 'description', 'category', 'network', 'symbol', 'launch_year', 'milestone', 'launch_date', 'stage', 'website', 'sources', 'status', 'published_at'];

    protected function casts(): array
    {
        return ['launch_year' => 'integer', 'launch_date' => 'date', 'published_at' => 'datetime'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('published_at', '<=', now());
    }
}
