<?php

namespace App\Models;

use App\Models\ReportImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportClient extends Model
{
    use HasFactory;
    protected $table = 'reportclient';

    protected $fillable = [
        'name',
        'phone',
        'cccd',
        'address',
        'birthday',
        'time',
        'description',
        'user_id',
        'post_id'
    ];

    protected $appends = ['user', 'post', 'images', 'card'];
    public function getUserAttribute()
    {
        return User::where('id', $this->attributes['user_id'])->first();
    }
    public function getPostAttribute()
    {
        return Post::where('id', $this->attributes['post_id'])->first();
    }
    public function getImagesAttribute()
    {
        return ReportImage::where('report_id', $this->attributes['id'])->get();
    }

    public function getCardAttribute()
    {
        return ReportCard::where('report_id', $this->attributes['id'])->get();
    }
}
