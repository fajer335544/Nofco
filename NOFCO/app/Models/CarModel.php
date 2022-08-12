<?php
namespace  App\Models;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'models';

    public  function products(){
        return $this->hasMany('App\Models\Product' , 'model_id' , 'id');
    }
    public  function brands(){
        return $this->belongsTo('App\Models\Brand' , 'brand_id' , 'id');
    }


    public function translations(){
        return $this->hasMany('App\Models\CarModelTranslation' , 'model_id' , 'id');
    }

    public function translation($locale){
        $data = $this->hasOne('App\Models\CarModelTranslation' , 'model_id' , 'id')->whereLocale($locale)->get();
        if($data->isNotEmpty())
            return $data->first();
        return $this;
    }

    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id' , 'id');
    }
}
