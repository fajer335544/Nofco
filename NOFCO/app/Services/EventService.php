<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Storage;

/**
 * Class EventService
 * @package App\Services
 */
class EventService
{


    function __construct()
    {
    }

    protected function resolveCriteria($data = [])
    {
        $query = Event::Query();
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

        if (array_key_exists('type', $data)) {
            $query = $query->where('type', $data['type']);
        }

        if (array_key_exists('category_id', $data)) {
            $query = $query->where('category_id', $data['category_id']);
        }
        if (array_key_exists('user_id', $data)) {
            $query = $query->where('user_id', $data['user_id']);
        }

        if (array_key_exists('between', $data)) {
            $query = $query->where('start_date' ,'<=', $data['between']);
            $query = $query->where('end_date' ,'>=', $data['between']);
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
            $res = Event::findOrFail($id);
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

    function create($dataIn = [], Event &$model)
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

    function update($dataIn = [], Event &$model)
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

    public function mapDataModel($data, Event &$model)
    {
        $attribute = [
            'category_id',
            'record_order',
            'visible',
            'name',
            'image',
            'file',
            'start_date',
            'end_date',
            'user_id',
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }
}
