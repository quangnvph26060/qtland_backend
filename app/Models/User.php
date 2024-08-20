<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'cccd',
        'birthday',
        'password',
        'phone',
        'address',
        'workunit',
        'role_id',
        'is_active',
    ];

    // public function searchableAs(): string
    // {
    //     return 'users_index';
    // }

    // public function toSearchableArray()
    // {
    //     return [
    //         'id' => $this->id,
    //         'name' => $this->name,
    //         'email' => $this->email,
    //         'role_id' => $this->role_id,
    //         'is_active' => $this->is_active,
    //     ];
    // }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function comment()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }

    public function post()
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }
    protected $appends = ['permissions'];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'user_id', 'id');
    }

    public function getPermissionsAttribute()
    {
        return $this->permissions()->get();
    }
}
