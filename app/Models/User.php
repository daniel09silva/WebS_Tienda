<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Supabase stores identity/auth data in the "auth.users" table
     * (managed by GoTrue), separate from app data in "public.profiles".
     */
    protected $table = 'auth.users';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'encrypted_password',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_confirmed_at' => 'datetime',
            'raw_app_meta_data' => 'array',
            'raw_user_meta_data' => 'array',
        ];
    }

    /**
     * Supabase's password hash column is "encrypted_password", not "password".
     */
    public function getAuthPassword(): string
    {
        return $this->attributes['encrypted_password'];
    }

    /**
     * "auth.users" has no remember_token column; Supabase manages sessions itself.
     * Remember-me is a no-op here rather than an error.
     */
    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // no-op: no remember_token column on auth.users
    }

    public function getRememberTokenName()
    {
        return '';
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'id', 'id');
    }
}
