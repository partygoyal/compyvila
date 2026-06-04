<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'mobile',
        'dob',
        'gender',
        'country',
        'state',
        'district',
        'city',
        'pincode',
        'profile_photo',
        'address',
        'occupation',
        'school_college',
        'instagram_url',
        'facebook_url',
        'youtube_url',
        'website_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
