<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    protected $hidden = [
        'updated_at', 'created_at',
    ];

    public function containers()
    {
        return $this->hasMany('App\Container', 'map_id');
    }
}
