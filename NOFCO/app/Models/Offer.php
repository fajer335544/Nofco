<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class offer extends Model
{
    protected  $table = 'offer';

    public  function  product(){
        return $this->belongsTo('App\Models\Product' , 'product_id' , 'id');
    }
}
