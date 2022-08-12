<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected  $table = 'links';
    public function menu(){
        return $this->belongsTo('App\Models\Menu' , 'menu_id' , 'id');
    }
    public function parent(){
        return $this->belongsTo('App\Models\Link' , 'parent' , 'id');
    }
    public function suns(){
        return $this->hasMany('App\Models\Link' , 'parent' , 'id');
    }
    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id' , 'id');
    }
    public function images(){
        return $this->hasMany('App\Models\File' , 'refer_id' , 'id')->whereRefer(get_class($this))->whereType('image')->whereVisible(1);
    }

    public function videos(){
        return $this->hasMany('App\Models\File' , 'refer_id' , 'id')->whereRefer(get_class($this))->whereType('video')->whereVisible(1);
    }

    public function files(){
        return $this->hasMany('App\Models\File' , 'refer_id' , 'id')->whereRefer(get_class($this))->whereType('files')->whereVisible(1);
    }

}
