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

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return asset('images/default.jpg');
        }

        $path = ltrim($this->photo, '/');

        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        return asset('images/default.jpg');
    }

    public function getPhotosUrlsAttribute()
    {
        if (!is_array($this->photos)) {
            return [];
        }

        $urls = [];

        foreach ($this->photos as $path) {
            $path = ltrim((string) $path, '/');

            if (Storage::disk('public')->exists($path)) {
                $urls[] = Storage::url($path);
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