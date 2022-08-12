<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';


    public function category(){
        return $this->belongsTo('App\Models\ArticleCategory' , 'category_id' , 'id');
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
