<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $hidden = [
        'updated_at', 'created_at',
    ];

    public function map()
    {
        return $this->belongsTo('App\Map', 'map_id');
    }

    public function items()
    {
        return $this->hasMany('App\Item', 'container_id');
    }
}
