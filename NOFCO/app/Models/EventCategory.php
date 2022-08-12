<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $table = 'event_categories';


    public function events(){
        return $this->hasMany('App\Models\Event' , 'category_id' , 'id');
    }
    public function translations(){
        return $this->hasMany('App\Models\EventCategoryTranslation' , 'category_id' , 'id');
    }
    public function translation($locale){
        $data = $this->hasOne('App\Models\EventCategoryTranslation' , 'category_id' , 'id')->whereLocale($locale)->get();
        if($data->isNotEmpty())
            return $data->first();
        return $this;
    }
    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id' , 'id');
    }
}
