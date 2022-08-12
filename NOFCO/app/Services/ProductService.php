<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProductService
 * @package App\Services
 */
class ProductService
{


    function __construct()
    {
    }

    protected function resolveCriteria($data = [])
    {
        $query = Product::Query();
        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        } else {
            $query = $query->select("*");
        }

        if (array_key_exists('id', $data)) {
            $query = $query->where('id', $data['id']);
        }

        if (array_key_exists('name', $data)) {
            $query = $query->where('name', $data['name']);
        }

        if (array_key_exists('post_date', $data)) {
            $query = $query->where('post_date', $data['post_date']);
        }

        if (array_key_exists('visible', $data)) {
            $query = $query->where('visible', $data['visible']);
        }

        if (array_key_exists('category_id', $data)) {
            $query = $query->where('category_id', $data['category_id']);
        }
        if (array_key_exists('parent', $data)) {
            $query = $query->where(function ($query) use ($data) {
                $query->whereRaw('category_id = ' . $data['parent'] .
                    ' or category_id in (select id from product_categories where parent=' . $data['parent'] . ') ');
            });
        }

        if (array_key_exists('type', $data)) {
            $query = $query->whereHas('category', function ($query) use ($data) {
                $query->where('type' , $data['type'] );
            });
        }
        if (array_key_exists('user_id', $data)) {
            $query = $query->where('user_id', $data['user_id']);
        }

        if (array_key_exists('new', $data)) {
            $query = $query->where('new', $data['new']);
        }

        if (array_key_exists('record_order_up', $data)) {
            $query = $query->where('record_order', '>', $data['record_order_up']);
        }

        if (array_key_exists('record_order_down', $data)) {
            $query = $query->where('record_order', '<', $data['record_order_down']);
        }

        if (array_key_exists('orderBy', $data)) {
            $query = $query->orderBy($data['orderBy'][0], $data['orderBy'][1]);
        } else {
            $query = $query->orderBy('record_order', 'DESC');
        }


        if (array_key_exists('limit', $data) && array_key_exists('offset', $data)) {
            $query = $query->take($data['limit']);
            $query = $query->skip($data['offset']);
        }

        return $query;
    }

    function getOne($id, $slug = '')
    {
        if ($id > 0) {
            $res = Product::findOrFail($id);
            return $res;
        } else {
            $res = $this->resolveCriteria(['slug' => $slug])->firstOrFail();
            return $res;

        }
    }

    /**
     * @param $criteria
     */
    function getList($criteria = [])
    {
        $res = $this->resolveCriteria($criteria)->get();
        return $res;

    }

    function create($dataIn = [], Product &$model)
    {

        if (array_key_exists('image', $dataIn)) {
            $dataIn['image'] = $dataIn['image']->store('uploads');
        }
        if (array_key_exists('file', $dataIn)) {
            $dataIn['file'] = $dataIn['file']->store('uploads');
        }
        $this->mapDataModel($dataIn, $model);
        $model->save();
    }

    function update($dataIn = [], Product &$model)
    {

        if (array_key_exists('image', $dataIn)) {
            $dataIn['image'] = $dataIn['image']->store('uploads');
        }
        if (array_key_exists('file', $dataIn)) {
            $dataIn['file'] = $dataIn['file']->store('uploads');
        }
        $this->mapDataModel($dataIn, $model);
        $model->save();
    }

    function delete($id)
    {
        $res = $this->getOne($id);
        if (Storage::exists($res->image)) {
            Storage::delete($res->image);
        }
        if (Storage::exists($res->file)) {
            Storage::delete($res->file);
        }
        $res->delete();
    }

    public function mapDataModel($data, Product &$model)
    {
        $attribute = [
            'category_id',
            'brand_id',
            'model_id',
            'country_id',
            'record_order',
            'visible',
            'name',
            'code',
            'image',
            'file',
            'start_date',
            'end_date',
            'end_request_date',
            'origin_price',
            'sell_price',
            'offer_price',
            'currency_id',
            'new',
            'user_id',
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                if($data[$val] !== '') {
                    $model->$val = $data[$val];
                }
            }
        }
    }
}
