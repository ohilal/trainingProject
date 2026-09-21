<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'ldap_id',
        'guid',
        'is_ldap_user',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the unique identifier for the user.
     * Laravel Fortify/Auth uses this to find the user.
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * Check if this user is an LDAP user.
     */
    public function isLdapUser(): bool
    {
        return $this->is_ldap_user;
    }
}