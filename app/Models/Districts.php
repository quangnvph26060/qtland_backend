<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Districts extends Model
{
    use HasFactory;
    protected $table= "districts";
    protected $fillable = [
        'city_id',
        'name',
        'id',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Quan hệ giữa District và Ward.
     * Một quận/huyện có nhiều phường/xã.
     */
    public function wards()
    {
        return $this->hasMany(Wards::class);
    }
}
