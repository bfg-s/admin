<?php

namespace Admin\Tests\Unit\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /**
     * The table associated with the model.
     * @return string
     */
    protected $table = 'test_users';

    /**
     * The attributes that are mass assignable.
     * @return array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be cast.
     * @return array
     */
    protected $casts = [
        'username' => 'string',
        'email' => 'string',
        'password' => 'string',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @return array
     */
    protected $hidden = [
        'password'
    ];
}
