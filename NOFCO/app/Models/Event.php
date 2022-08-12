<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';


    public function category(){
        return $this->belongsTo('App\Models\EventCategory' , 'category_id' , 'id');
    }

    public function translations(){
        return $this->hasMany('App\Models\EventTranslation' , 'event_id' , 'id');
    }

    public function translation($locale){
        $data = $this->hasOne('App\Models\EventTranslation' , 'event_id' , 'id')->whereLocale($locale)->get();
        if($data->isNotEmpty())
            return $data->first();
        return null;
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
