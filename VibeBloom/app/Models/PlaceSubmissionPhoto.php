<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceSubmissionPhoto extends Model
{
    protected $fillable = [
        'place_submission_id',
        'path',
    ];

    public function submission()
    {
        return $this->belongsTo(PlaceSubmission::class, 'place_submission_id');
    }
}