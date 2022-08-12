<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $table = 'blocks';


    public function category(){
        return $this->belongsTo('App\Models\BlockCategory' , 'category_id' , 'id');
    }
    public function translations(){
        return $this->hasMany('App\Models\BlockTranslation' , 'block_id' , 'id');
    }

    public function translation($locale){
        $data = $this->hasOne('App\Models\BlockTranslation' , 'block_id' , 'id')->whereLocale($locale)->get();
        if($data->isNotEmpty())
            return $data->first();
        return null;
    }

    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id' , 'id');
    }

    public function images(){
        return $this->hasMany('App\Models\File' , 'refer_id' , 'id')->whereRefer(get_class($this))->whereType('image')->whereVisible(1)->orderBy('record_order','DESC');
    }

    public function videos(){
        return $this->hasMany('App\Models\File' , 'refer_id' , 'id')->whereRefer(get_class($this))->whereType('video')->whereVisible(1);
    }

    public function files(){
        return $this->hasMany('App\Models\File' , 'refer_id' , 'id')->whereRefer(get_class($this))->whereType('files')->whereVisible(1);
    }
}
