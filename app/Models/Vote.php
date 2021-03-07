<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;
    protected $fillable = [
        'type_id',
        'product_id',
        'user_id'
    ];

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function voteType()
    {
        return $this->belongsTo('App\VoteTypes');
    }
}
