<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected  $table = 'pages';

    public function translations(){
        return $this->hasMany('App\Models\PageTranslation' , 'page_id' , 'id');
    }

    public function translation($locale){
        $data = $this->hasOne('App\Models\PageTranslation' , 'page_id' , 'id')->whereLocale($locale)->get();
        if ($data->isNotEmpty())
            return $data->first();
        return
            $this->translations->first();
    }
}
