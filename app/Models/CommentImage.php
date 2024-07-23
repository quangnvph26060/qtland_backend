<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentImage extends Model
{
    use HasFactory;
    protected $table = 'comments_image';

    protected $fillable = [
        'comment_id',
        'post_id',
        'image_path',
    ];

    function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    function post()
    {
        return $this->belongsTo(Post::class);
    }
}
