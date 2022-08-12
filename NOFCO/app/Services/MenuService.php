<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\Storage;

/**
 * Class MenuService
 * @package App\Services
 */
class MenuService
{


    function __construct()
    {
    }

    protected function resolveCriteria($data = [])
    {
        $query = Menu::Query();
        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        } else {
            $query = $query->select("*");
        }

        if (array_key_exists('id', $data)) {
            $query = $query->where('id', $data['id']);
        }

        if (array_key_exists('name', $data)) {
            $query = $query->where('menus.name', $data['slug']);
        }

        if (array_key_exists('refer', $data)) {
            $query = $query->where('menus.refer', $data['refer']);
        }

        if (array_key_exists('locale', $data)) {
            $query = $query->where('menus.locale', $data['locale']);
        }

        if (array_key_exists('links_type', $data)) {
            $query = $query->whereHas('links', function ($query) use ($data) {
                if (is_array($data['links_type']))
                    $query->whereIn('type', $data['links_type']);
                else
                    $query->where('type', $data['links_type']);
            });
        }
        if (array_key_exists('record_order_up', $data)) {
            $query = $query->where('menus.record_order', '>', $data['record_order_up']);
        }

        if (array_key_exists('record_order_down', $data)) {
            $query = $query->where('menus.record_order', '<', $data['record_order_down']);
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
            $res = Menu::findOrFail($id);
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

    function create($dataIn = [], Menu &$article)
    {

        $this->mapDataModel($dataIn, $article);
        $article->save();
    }

    function update($dataIn = [], Menu &$article)
    {

        $this->mapDataModel($dataIn, $article);
        $article->save();
    }

    function delete($id)
    {

        $res = $this->getOne($id);

        $res->delete();
    }

    public function mapDataModel($data, Menu &$model)
    {
        $attribute = [
            'name'
            , 'col'
            , 'col_sm'
            , 'col_md'
            , 'col_lg'
            , 'col_xl'
            , 'record_order'
            , 'refer'
            , 'locale'
            , 'visible'
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }
}
