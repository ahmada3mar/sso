<?php

namespace Hyperpay\SSO\Models;

use Illuminate\Foundation\Auth\User as AppUser;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Exception;

class User extends AppUser implements JWTSubject, AuthenticatableContract
{
    use  HasRoles, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token',
        'roles'
    ];

    public function getTable()
    {
        throw new Exception("Database not allowed");
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAllPermissions()
    {
        return $this->permissions;
    }

    public function hasPermissionTo($name)
    {
        return \in_array($name, $this->getAllPermissions()->pluck('name')->toArray());
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getRememberToken()
    {
        return null;
    }
    public function setRememberToken($value)
    {
    }
    public function getRememberTokenName()
    {
    }

    protected function getDefaultGuardName()
    {
        return 'api';
    }
}
