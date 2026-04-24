<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class UsersModel extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'users';

    protected $primaryKey = 'uid';

    public $timestamps = false;

    protected $fillable = [
        'user',
        'username',
        'password',
        'company_id',
        'role'
    ];

    protected $hidden = [
        'password'
    ];
}
