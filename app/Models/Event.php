<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public $timestamps = false;

    protected $fillable = ['title', 'description', 'event_at', 'category', 'source', 'verified_at'];
}
