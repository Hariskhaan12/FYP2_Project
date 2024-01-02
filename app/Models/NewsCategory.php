<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    protected $fillable = [
        'name', //unique
        'slug',
        'status',
    ];

    public function news(){
        return $this->hasMany(News::class);
    }
}
