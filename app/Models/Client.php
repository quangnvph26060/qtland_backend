<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'phone',
        'cccd',
        'address',
        'email',
        'finance',
        'searcharea',
        'area',
        'intendtime',
        'business',
        'personnumber',
        'numbercars',
        'numbermotor',
        'note',
        'birth_year',
        'user_id'
    ];

    protected $appends = ['user'];

    public function getUserAttribute()
    {
        return User::where('id', $this->attributes['user_id'])->first();
    }
}
