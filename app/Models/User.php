<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    
    protected $fillable = [
        'nama', 'email', 'password', 'role', 'api_token'
    ];

    protected $hidden = [
        'password',
    ];
}