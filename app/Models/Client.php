<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

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
    ];
}
