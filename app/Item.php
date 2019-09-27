<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function container()
    {
        return $this->belongsTo('App\Container', 'container_id');
    }
}
