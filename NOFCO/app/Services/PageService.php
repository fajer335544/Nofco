<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Facades\Storage;

/**
 * Class PageService
 * @package App\Services
 */
class PageService
{


    function __construct()
    {
    }

    /**
     * @param $criteria
     */
    function getList($criteria = [])
    {
        $res = $this->resolveCriteria($criteria)->get();
        return $res;
    }

    protected function resolveCriteria($data = [])
    {
        $query = Page::Query();

        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        }

        if (array_key_exists('id', $data)) {
            $query = $query->where('id', $data['category_id']);
        }


        if (array_key_exists('type', $data)) {
            $query = $query->where('type', (int)$data['type']);
        }

        if (array_key_exists('quantity', $data)) {
            $query = $query->where('quantity', (int)$data['quantity']);
        }

        if (array_key_exists('name', $data)) {
            $query = $query->where('name', 'LIKE', "'%" . $data['name'] . "%'");
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

    function create($dataIn = [], Page &$Page)
    {

        if (array_key_exists('image', $dataIn)) {
            $dataIn['image'] = $dataIn['image']->store('uploads');
        }
        $this->mapDataModel($dataIn, $Page);
        $Page->save();
    }

    public function mapDataModel($data, Page &$model)
    {
        $attribute = [
            'record_order',
            'name',
            'type',
            'image',
            'quantity',
            'user_id',
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }

    function update($dataIn = [], Page &$Page)
    {
        if (array_key_exists('image', $dataIn)) {
            $dataIn['image'] = $dataIn['image']->store('uploads');
        }
        $this->mapDataModel($dataIn, $Page);
        $Page->save();
    }

    function delete($id)
    {

        $res = $this->getOne($id);
        if (Storage::exists($res->image)) {
            Storage::delete($res->image);
        }
        $res->delete();
    }

    function getOne($id, $slug = '')
    {
        if ($id > 0) {
            $res = Page::findOrFail($id);
            return $res;
        } else {

            $res = $this->resolveCriteria(['slug' => $slug])->firstOrFail();
            return $res;

        }
    }
}
