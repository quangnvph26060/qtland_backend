<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $table= "permissions";
    protected $fillable = [
        'user_id',
        'role_id',
        'access_permission_1',
        'access_permission_2',
        'access_permission_3',
        'access_permission_4',
        'access_permission_5',
    ];
}
