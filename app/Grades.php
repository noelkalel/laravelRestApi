<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grades extends Model
{
    protected $guarded = [];

    protected $dates = [
        'date_created'
    ];

    public function ads()
    {
        return $this->belongsTo(Ads::class);
    }
}