<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            CompetitionCategory::class,
            'competition_category_id'
        );
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(CompetitionRound::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(CompetitionRegistration::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function publicVotes(): HasMany
    {
        return $this->hasMany(PublicVote::class);
    }

    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }
}
