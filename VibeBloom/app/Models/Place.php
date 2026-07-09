<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Place extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'city',
        'address',
        'reference',
        'type',
        'rating',
        'price',
        'photo',
        'photos',
        'lat',
        'lng',
        'description',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    protected $appends = [
        'photo_url',
        'photos_urls',
    ];

    private function publicPhotoUrl(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
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

        return null;
    }

    public function getPhotoUrlAttribute()
    {
        return $this->publicPhotoUrl($this->photo) ?: asset('images/vibebloom.png');
    }

    public function getPhotosUrlsAttribute()
    {
        if (!is_array($this->photos)) {
            return [];
        }

        $urls = [];

        foreach ($this->photos as $path) {
            $url = $this->publicPhotoUrl($path);

            if ($url) {
                $urls[] = $url;
            }
        }

        return array_values($urls);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
