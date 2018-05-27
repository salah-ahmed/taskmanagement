<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lists extends Model
{
    public function items()
    {
        return $this->hasMany('App\Item', 'list_id')->select('description');
    }
}