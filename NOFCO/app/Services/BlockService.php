<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\Block;
use Illuminate\Support\Facades\Storage;

/**
 * Class BlockService
 * @package App\Services
 */
class BlockService
{


    function __construct()
    {
    }

    protected function resolveCriteria($data = [])
    {
        $query = Block::Query();
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

        if (array_key_exists('phone', $data)) {
            $query = $query->where('phone', $data['phone']);
        }

        if (array_key_exists('start_date', $data)) {
            $query = $query->where('start_date', $data['start_date']);
        }
        if (array_key_exists('end_date', $data)) {
            $query = $query->where('end_date', $data['end_date']);
        }

        if (array_key_exists('date_in', $data)) {

            $query = $query->where('start_date' ,'>=' , $data['date_in']);
            $query = $query->where('end_date', '<=' , $data['date_in']);
        }
        if (array_key_exists('visible', $data)) {
            $query = $query->where('visible', $data['visible']);
        }

        if (array_key_exists('category_id', $data)) {
            $query = $query->where('category_id', $data['category_id']);
        }

        if (array_key_exists('category_id_in', $data)) {
            $query = $query->whereIn('category_id', $data['category_id_in']);
        }
        if (array_key_exists('user_id', $data)) {
            $query = $query->where('user_id', $data['user_id']);
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
            $res = Block::findOrFail($id);
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

    function create($dataIn = [], Block &$model)
    {

        if (array_key_exists('image', $dataIn)) {
            $dataIn['image'] = $dataIn['image']->store('uploads');
        }
        if (array_key_exists('image_one', $dataIn)) {
            $dataIn['image_one'] = $dataIn['image_one']->store('uploads');
        }
        if (array_key_exists('image_two', $dataIn)) {
            $dataIn['image_two'] = $dataIn['image_two']->store('uploads');
        }
        if (array_key_exists('image_three', $dataIn)) {
            $dataIn['image_three'] = $dataIn['image_three']->store('uploads');
        }
        if (array_key_exists('image_four', $dataIn)) {
            $dataIn['image_four'] = $dataIn['image_four']->store('uploads');
        }
        if (array_key_exists('file', $dataIn)) {
            $dataIn['file'] = $dataIn['file']->store('uploads');
        }
        $this->mapDataModel($dataIn, $model);
        $model->save();
    }

    function update($dataIn = [], Block &$model)
    {

        if (array_key_exists('image', $dataIn)) {
            $dataIn['image'] = $dataIn['image']->store('uploads');
        }
        if (array_key_exists('image_one', $dataIn)) {
            $dataIn['image_one'] = $dataIn['image_one']->store('uploads');
        }
        if (array_key_exists('image_two', $dataIn)) {
            $dataIn['image_two'] = $dataIn['image_two']->store('uploads');
        }
        if (array_key_exists('image_three', $dataIn)) {
            $dataIn['image_three'] = $dataIn['image_three']->store('uploads');
        }
        if (array_key_exists('image_four', $dataIn)) {
            $dataIn['image_four'] = $dataIn['image_four']->store('uploads');
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
        if (Storage::exists($res->image_one)) {
            Storage::delete($res->image_one);
        }
        if (Storage::exists($res->image_two)) {
            Storage::delete($res->image_two);
        }
        if (Storage::exists($res->image_three)) {
            Storage::delete($res->image_three);
        }
        if (Storage::exists($res->image_four)) {
            Storage::delete($res->image_four);
        }
        if (Storage::exists($res->file)) {
            Storage::delete($res->file);
        }
        $res->delete();
    }

    public function mapDataModel($data, Block &$model)
    {
        $attribute = [
            'category_id',
            'record_order',
            'visible',
            'slug',
            'name',
            'image',
            'image_one',
            'image_two',
            'image_three',
            'image_four',
            'file',
            'url',
            'phone',
            'post_date',
            'user_id',
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }
}
