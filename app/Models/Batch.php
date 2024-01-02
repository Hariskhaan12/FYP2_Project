<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{

    protected $fillable = ['name'];

    public function alumni()
    {
        return $this->hasMany(Alumni::class);
    }
}
