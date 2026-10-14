<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Snapshot extends Model
{
    public $timestamps = false;

    protected $fillable = ['symbol', 'name', 'price', 'change_pct', 'captured_at', 'source'];

    protected function casts(): array
    {
        return ['price' => 'decimal:6', 'change_pct' => 'decimal:2'];
    }
}
