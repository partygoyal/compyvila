<?php

namespace App\Models;

use App\Notifications\JudgeAssignedToCompetition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JudgeAssignment extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::created(function (JudgeAssignment $assignment) {
            $assignment->load([
                'competition',
                'round',
                'judge',
            ]);

            try {
                $assignment->judge->notify(
                    new JudgeAssignedToCompetition($assignment)
                );
            } catch (\Throwable $exception) {
                report($exception);
            }
        });
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(
            CompetitionRound::class,
            'competition_round_id'
        );
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'judge_id'
        );
    }
}
