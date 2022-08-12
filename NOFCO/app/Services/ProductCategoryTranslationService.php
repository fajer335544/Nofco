<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\ProductCategoryTranslation;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProductCategoryTranslationService
 * @package App\Services
 */
class ProductCategoryTranslationService
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
        $query = ProductCategoryTranslation::Query();

        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        }

        if (array_key_exists('id', $data)) {
            $query = $query->where('id', $data['id']);
        }

        if (array_key_exists('category_id', $data)) {
            $query = $query->where('category_id', $data['category_id']);
        }


        if (array_key_exists('locale', $data)) {
            $query = $query->where('locale', $data['locale']);
        }

        if (array_key_exists('name', $data)) {
            $query = $query->where('name', 'LIKE', "'%" . $data['name'] . "%'");
        }

        if (array_key_exists('limit', $data) && array_key_exists('offset', $data)) {
            $query = $query->take($data['limit']);
            $query = $query->skip($data['offset']);
        }

        return $query;
    }

    function create($dataIn = [], ProductCategoryTranslation &$ProductCategoryTranslation)
    {

        if (array_key_exists('name', $dataIn))
            $dataIn['slug'] = make_slug($dataIn['name'], '-');
        $this->mapDataModel($dataIn, $ProductCategoryTranslation);
        $ProductCategoryTranslation->save();
    }

    public function mapDataModel($data, ProductCategoryTranslation &$model)
    {
        $attribute = [
            'category_id',
            'name',
            'description',
            'slug',
            'locale',
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }

    function update($dataIn = [], ProductCategoryTranslation &$ProductCategoryTranslation)
    {
        if (array_key_exists('name', $dataIn))
            $dataIn['slug'] = make_slug($dataIn['name'], '-');
        $this->mapDataModel($dataIn, $ProductCategoryTranslation);
        $ProductCategoryTranslation->save();
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
            $res = ProductCategoryTranslation::findOrFail($id);
            return $res;
        } else {

            $res = $this->resolveCriteria(['slug' => $slug])->firstOrFail();
            return $res;

        }
    }
}
