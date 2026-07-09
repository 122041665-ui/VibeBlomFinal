<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PlaceSubmissionPhoto extends Model
{
    protected $fillable = [
        'place_submission_id',
        'path',
    ];

    protected $appends = [
        'url',
    ];

    public function getUrlAttribute(): string
    {
        $path = trim((string) $this->path);

        if ($path === '') {
            return asset('images/vibebloom.png');
        }

        if (
            str_starts_with($path, 'http://') ||
            str_starts_with($path, 'https://') ||
            str_starts_with($path, '//') ||
            str_starts_with($path, 'data:')
        ) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return asset(ltrim($path, '/'));
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        $path = ltrim($path, '/');

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset('images/vibebloom.png');
    }

    public function submission()
    {
        return $this->belongsTo(PlaceSubmission::class, 'place_submission_id');
    }
}
