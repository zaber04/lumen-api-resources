<?php

namespace Zaber04\LumenApiResources\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use HasUuids;
    use Authenticatable;
    use Authorizable;
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'contact', 'address' /*, role */
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * Archive an user
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'contact'           => 'string',  // can apply phone no format actually
        'address'           => 'string'
    ];

    /**
     * Validation rules.
     *
     * @var array<string, string>
     */
    public static $rules = [
        'last_name'  => 'required|string|max:255',
        'first_name' => 'required|string|max:255',
        'email'      => 'required|email|unique:users|max:255', // check unique for registration only
        'password'   => 'required|string|min:8|max:255',
        'contact'    => 'nullable|string|max:20',
        'address'    => 'nullable|string|max:255'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
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
        $customClaims = [
            'user_id' => $this->id
        ];

        return $customClaims;
    }

    /**
     * Set the password attribute and automatically hash it.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Accessor to get the full name attribute.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
