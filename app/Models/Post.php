<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Redis;


class Post extends Model
{
    use HasFactory;


    // protected static function booted()
    // {
    //     static::saved(function ($post) {
    //         Redis::flushAll();
    //     });

    //     static::deleted(function ($post) {
    //         Redis::flushAll();
    //     });
    // }


    protected $fillable = [
        'title',
        'description',
        'address',
        'address_detail',
        'classrank',
        'area',
        'areausable',
        'price',
        'priceservice',
        'priceElectricity',
        'pricewater',
        'floors',
        'rooms',
        'bathrooms',
        'bonus',
        'bonusmonthly',
        'direction',
        'directionBalcony',
        'wayin',
        'font',
        'pccc',
        'elevator',
        'stairs',
        'unit',
        'unit1',
        'unit2',
        'unit3',
        'sold_status',
        'status_id',
        'priority_status',
        'user_id',
    ];

    // public function searchableAs(): string
    // {
    //     return 'posts_index';
    // }

    // public function toSearchableArray()
    // {
    //     return [
    //         'id' => $this->id,
    //         'title' => $this->title,
    //         'address' => $this->address,
    //         'address_detail' => $this->address_detail,
    //     ];
    // }
    protected $appends = ['user_info'];
    public function getUserInfoAttribute()
    {
        return User::where('id', $this->attributes['user_id'])->first();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function comment()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function postImage()
    {
        return $this->hasMany(PostImage::class, 'post_id', 'id');
    }

    public function views()
    {
        return $this->hasMany(PostView::class)->with('user');
    }
}
