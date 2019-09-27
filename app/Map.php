<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    public function containers()
    {
        return $this->hasMany('App\Container', 'map_id');
    }
}
