<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Story extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'thumbnail',
        'body',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
