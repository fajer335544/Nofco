<?php
namespace  App\Models;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'brands';

    public  function products(){
        return $this->hasMany('App\Models\Product' , 'brand_id' , 'id');
    }
    public  function models(){
        return $this->hasMany('App\Models\CarModel' , 'model_id' , 'id');
    }
    public function brands(){
        return $this->hasMany('App\Models\Brand' , 'brand_id' , 'id');
    }

    public function translations(){
        return $this->hasMany('App\Models\BrandTranslation' , 'brand_id' , 'id');
    }

    public function translation($locale){
        $data = $this->hasOne('App\Models\BrandTranslation' , 'brand_id' , 'id')->whereLocale($locale)->get();
        if($data->isNotEmpty())
            return $data->first();
        return $this;
    }

    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id' , 'id');
    }
}
