<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'nama_user',
        'pass_user',
        'role',
    ];

    protected $hidden = [
        'pass_user',
    ];

    public function getAuthPassword()
    {
        return $this->pass_user;
    }
}