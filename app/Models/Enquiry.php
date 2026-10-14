<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'email', 'company', 'placement', 'message', 'created_at'];
}
