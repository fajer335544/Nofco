<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';


    public function category()
    {
        return $this->belongsTo('App\Models\ProductCategory', 'category_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency', 'currency_id', 'id');
    }

    public function nations()
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }

    public function brands()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id', 'id');
    }

    public function models()
    {
        return $this->belongsTo('App\Models\CarModel', 'model_id', 'id');
    }

    public function colors()
    {
        return $this->belongsTo('App\Models\Color', 'color_id', 'id');
    }

    public function translations()
    {
        return $this->hasMany('App\Models\ProductTranslation', 'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function translation($locale)
    {
        $data = $this->hasOne('App\Models\ProductTranslation', 'product_id', 'id')->whereLocale($locale)->get();
        if ($data->isNotEmpty())
            return $data->first();
        return
            $this->translations->first();
    }

    public function images()
    {
        return $this->hasMany('App\Models\File', 'refer_id', 'id')->whereRefer(get_class($this))->whereType('image')->whereVisible(1);
    }

    public function videos()
    {
        return $this->hasMany('App\Models\File', 'refer_id', 'id')->whereRefer(get_class($this))->whereType('video')->whereVisible(1);
    }

    public function files()
    {
        return $this->hasMany('App\Models\File', 'refer_id', 'id')->whereRefer(get_class($this))->whereType('files')->whereVisible(1);
    }
}
