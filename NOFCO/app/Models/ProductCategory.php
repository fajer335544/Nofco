<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_categories';


    public function products(){
        return $this->hasMany('App\Models\Product' , 'category_id' , 'id');
    }
    public function translations(){
        return $this->hasMany('App\Models\ProductCategoryTranslation' , 'category_id' , 'id');
    }
    public function translation($locale){
        $data = $this->hasOne('App\Models\ProductCategoryTranslation' , 'category_id' , 'id')->whereLocale($locale)->get();
        if($data->isNotEmpty())
            return $data->first();
        return $this;
    }
    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id' , 'id');
    }

    public function suns(){
        return $this->hasMany('App\Models\ProductCategory' , 'parent' , 'id');
    }
    public function parents(){
        return $this->belongsTo('App\Models\ProductCategory' , 'parent' , 'id');
    }
}
