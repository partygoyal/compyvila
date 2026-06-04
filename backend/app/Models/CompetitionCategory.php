<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionCategory extends Model
{
    protected $guarded = [];

    public function competitions(): HasMany
    {
        return $this->hasMany(
            Competition::class,
            'competition_category_id'
        );
    }
}