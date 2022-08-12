<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    protected $table = 'article_categories';


    public function articles(){
        return $this->hasMany('App\Models\Article' , 'category_id' , 'id');
    }
    public function translations(){
        return $this->hasMany('App\Models\ArticleCategoryTranslation' , 'category_id' , 'id');
    }

    public function translation($locale){
         $data = $this->hasOne('App\Models\ArticleCategoryTranslation' , 'category_id' , 'id')->whereLocale($locale)->get();
         if($data->isNotEmpty())
             return $data->first();
         return null;
    }
    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id' , 'id');
    }
}
