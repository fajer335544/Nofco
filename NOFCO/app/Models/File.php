<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected  $table = 'files';

    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id' , 'id');
    }
    public function translations(){
        return $this->hasMany('App\Models\FileTranslation' , 'file_id' , 'id');
    }
    public function translation($locale){
        $data = $this->hasOne('App\Models\FileTranslation' , 'file_id' , 'id')->whereLocale($locale)->get();
        if($data->isNotEmpty())
            return $data->first();
        return $this;
    }
}
