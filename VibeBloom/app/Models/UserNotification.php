<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'title',
        'body',
        'url',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public static function sendTo(User|int|null $user, string $type, string $title, ?string $body = null, ?string $url = null, ?User $actor = null, array $data = []): ?self
    {
        $userId = $user instanceof User ? $user->id : $user;

        if (!$userId) {
            return null;
        }

        return self::create([
            'user_id' => $userId,
            'actor_id' => $actor?->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'data' => $data ?: null,
        ]);
    }
}
