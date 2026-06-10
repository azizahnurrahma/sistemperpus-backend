<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    // Nama tabel di database
    protected $table = 'users';

    // Memberitahu Lumen bahwa Primary Key-nya adalah id_user sesuai laporan
    protected $primaryKey = 'id_user';

    // Kolom murni sesuai kamus data Tabel User di laporan (Tanpa kolom nama)
    protected $fillable = [
        'email', 'password', 'role', 'api_token'
    ];

    // Menyembunyikan password saat data user dipanggil
    protected $hidden = [
        'password', 'api_token'
    ];
}