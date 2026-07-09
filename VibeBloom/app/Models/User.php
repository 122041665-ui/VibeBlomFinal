<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasProfilePhoto;
    use HasRoles;  // 👈 NECESARIO PARA PERMISOS Y ROLES

    /**
     * Campos asignables en masa
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'external_profile_photo_url',
        'platform_user_id',
        'profile_is_public',
    ];

    /**
     * Campos ocultos
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'profile_is_public' => 'boolean',
    ];

    /**
     * Accesorios agregados al modelo
     */
    protected $appends = [
        'profile_photo_url',
        'display_photo_url',
    ];

    /**
     * RELACIÓN: Un usuario tiene muchos lugares
     */
    public function places()
    {
        return $this->hasMany(Place::class);
    }

    /**
     * RELACIÓN: Favoritos muchos a muchos
     */
    public function favorites()
    {
        return $this->belongsToMany(Place::class, 'favorites')
                    ->withTimestamps();
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'followed_id', 'follower_id')
                    ->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'followed_id')
                    ->withTimestamps();
    }

    public function isFollowing(User $user): bool
    {
        if (!$this->exists || !$user->exists || (int) $this->id === (int) $user->id) {
            return false;
        }

        return $this->following()
            ->where('users.id', $user->id)
            ->exists();
    }

    public function getDisplayPhotoUrlAttribute(): string
    {
        $external = trim((string) ($this->external_profile_photo_url ?? ''));

        if ($external !== '') {
            if (
                str_starts_with($external, 'http://') ||
                str_starts_with($external, 'https://') ||
                str_starts_with($external, '//') ||
                str_starts_with($external, 'data:')
            ) {
                return $external;
            }

            if (str_starts_with($external, '/')) {
                return asset(ltrim($external, '/'));
            }

            if (str_starts_with($external, 'storage/')) {
                return asset($external);
            }

            return asset('storage/' . ltrim($external, '/'));
        }

        if ($this->profile_photo_path) {
            return Storage::disk($this->profilePhotoDisk())->url($this->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name)
            . '&color=2563EB&background=EFF6FF&bold=true';
    }
}
