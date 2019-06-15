<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'valid_until'
    ];

    public function grades()
    {
        return $this->hasMany(Grades::class);
    }
}
