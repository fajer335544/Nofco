<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\ArticleCategory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

/**
 * Class ArticleCategoryService
 * @package App\Services
 */
class ArticleCategoryService
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
        $query = ArticleCategory::Query();

        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        }

        if (array_key_exists('id', $data)) {
            $query = $query->where('id', $data['category_id']);
        }


        if (array_key_exists('type', $data)) {
            $query = $query->where('type', (int)$data['type']);
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

    function create($dataIn = [], ArticleCategory &$ArticleCategory)
    {

        if (array_key_exists('image', $dataIn)) {
            $dataIn['image'] = $dataIn['image']->store('uploads');
        }
        $this->mapDataModel($dataIn, $ArticleCategory);
        $ArticleCategory->save();
    }

    public function mapDataModel($data, ArticleCategory &$model)
    {
        $attribute = [
            'record_order',
            'name',
            'description',
            'type',
            'image',
            'user_id',
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }

    function update($dataIn = [], ArticleCategory &$ArticleCategory)
    {
        if (array_key_exists('image', $dataIn)) {
            $dataIn['image'] = $dataIn['image']->store('uploads');
        }
        $this->mapDataModel($dataIn, $ArticleCategory);
        $ArticleCategory->save();
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
            $res = ArticleCategory::findOrFail($id);
            return $res;
        } else {

            $res = $this->resolveCriteria(['slug' => $slug])->firstOrFail();
            return $res;

        }
    }

    public function generate_urls($category, &$array = [])
    {
        $array[] = "<a href=\"" . url(
                App::getLocale() . '/articles/'
                . $category->id . '/' .
                $category->translation(App::getLocale())->slug) .
            "\">"
            . $category->translation(App::getLocale())->name
            . "</a>";

        return array_reverse($array);

    }
}
