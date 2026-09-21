<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use LdapRecord\Laravel\Auth\HasLdapAuthentication;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;

class User extends Authenticatable implements LdapAuthenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasLdapAuthentication;

    protected $fillable = [
        'name',
        'email',
        'password',
        'ldap_id', // Add this field to store LDAP user identifier
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function Role(): Role
    {
        return Role::where('id', $this->getOriginal('pivot_role_id'))->first();
    }

    public function Terms(): BelongsToMany
    {
        return $this->belongsToMany(
            Term::class,
            'term_user',
            'user_id',
            'term_id'
        )->withPivot('id', 'role_id');
    }

    public function Participants(): HasMany
    {
        return $this->hasMany(Participant::class, 'user_id');
    }

    public function CoinsLogs()
    {
        return $this->hasMany(CoinsLog::class, 'user_id');
    }
}