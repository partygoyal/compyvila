<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(function ($review) {

            $review->total_score =
                ($review->creativity ?? 0) +
                ($review->presentation ?? 0) +
                ($review->quality ?? 0) +
                ($review->performance ?? 0);
        });
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'judge_id'
        );
    }
}