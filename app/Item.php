<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $hidden = [
        'updated_at', 'created_at',
    ];

    public function container()
    {
        return $this->belongsTo('App\Container', 'container_id');
    }
}
