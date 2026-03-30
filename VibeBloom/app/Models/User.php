<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
    ];

    /**
     * Accesorios agregados al modelo
     */
    protected $appends = [
        'profile_photo_url',
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
}
