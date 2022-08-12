<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockCategory extends Model
{
    protected $table = 'block_categories';


    public function blocks(){
        return $this->hasMany('App\Models\Block' , 'category_id' , 'id');
    }
    public function translations(){
        return $this->hasMany('App\Models\BlockCategoryTranslation' , 'category_id' , 'id');
    }

    public function translation($locale){
        $data = $this->hasOne('App\Models\BlockCategoryTranslation' , 'category_id' , 'id')->whereLocale($locale)->get();
        if($data->isNotEmpty())
            return $data->first();
        return $this;
    }

    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id' , 'id');
    }
}
