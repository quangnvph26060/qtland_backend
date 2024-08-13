<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    use HasFactory;
    protected $table = 'report_card';
    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'image'
    ];
}
