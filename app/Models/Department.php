<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'short_name'];
    public function alumni()
    {
        return $this->hasMany(Alumni::class);
    }
}
