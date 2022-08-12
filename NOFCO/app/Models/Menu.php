<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';

    public function links(){
        return $this->hasMany('App\Models\Link' , 'menu_id' , 'id');
    }
}
