<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wards extends Model
{
    use HasFactory;
    protected $table="wards";
    protected $fillable = [
        'district_id',
        'name',
        'id',
    ];

    public function district()
    {
        return $this->belongsTo(Districts::class);
    }
}
