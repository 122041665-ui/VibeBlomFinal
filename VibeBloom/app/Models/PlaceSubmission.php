<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'rating',
        'price',
        'city',
        'city_place_id',
        'address',
        'lat',
        'lng',
        'description',
        'status',
        'sent_to_flask',
        'sent_to_flask_at',
    ];

    protected $casts = [
        'sent_to_flask' => 'boolean',
        'sent_to_flask_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->hasMany(PlaceSubmissionPhoto::class);
    }
}