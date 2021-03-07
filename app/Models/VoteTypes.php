<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteTypes extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'icon'
    ];

    public function vote()
    {
        return $this->hasMany('App\Vote');
    }
}
