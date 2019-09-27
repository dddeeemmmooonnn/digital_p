<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    public function map()
    {
        return $this->belongsTo('App\Map', 'map_id');
    }

    public function items()
    {
        return $this->hasMany('App\Item', 'container_id');
    }
}
