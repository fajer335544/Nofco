<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\ProductTranslation;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProductTranslationService
 * @package App\Services
 */
class ProductTranslationService
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
        $query = ProductTranslation::Query();

        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        }

        if (array_key_exists('id', $data)) {
            $query = $query->where('id', $data['id']);
        }

        if (array_key_exists('condition_car', $data)) {
            $query = $query->where('condition_car', $data['condition_car']);
        }

        if (array_key_exists('interior_color', $data)) {
            $query = $query->where('interior_color', $data['interior_color']);
        }

        if (array_key_exists('fuel_type', $data)) {
            $query = $query->where('fuel_type', $data['fuel_type']);
        }

        if (array_key_exists('engine', $data)) {
            $query = $query->where('engine', $data['engine']);
        }

        if (array_key_exists('cylinder', $data)) {
            $query = $query->where('cylinder', $data['cylinder']);
        }

        if (array_key_exists('cabin', $data)) {
            $query = $query->where('cabin', $data['cabin']);
        }

        if (array_key_exists('milage', $data)) {
            $query = $query->where('milage', $data['milage']);
        }

        if (array_key_exists('product_id', $data)) {
            $query = $query->where('product_id', $data['product_id']);
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

    function create($dataIn = [], ProductTranslation &$ProductTranslation)
    {

        if (array_key_exists('name', $dataIn))
            $dataIn['slug'] = make_slug($dataIn['name'], '-');
        $this->mapDataModel($dataIn, $ProductTranslation);
        $ProductTranslation->save();
    }

    public function mapDataModel($data, ProductTranslation &$model)
    {
        $attribute = [
            'product_id',
            'name',
            'description',
            'condition_car',
            'interior_color',
            'fuel_type',
            'engine',
            'cylinder',
            'cabin',
            'milage',
            'model_year',
            'slug',
            'locale',
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }

    function update($dataIn = [], ProductTranslation &$ProductTranslation)
    {
        if (array_key_exists('name', $dataIn))
            $dataIn['slug'] = make_slug($dataIn['name'], '-');
        $this->mapDataModel($dataIn, $ProductTranslation);
        $ProductTranslation->save();
    }

    function delete($id)
    {

        $res = $this->getOne($id);

        $res->delete();
    }

    function getOne($id, $slug = '')
    {
        if ($id > 0) {
            $res = ProductTranslation::findOrFail($id);
            return $res;
        } else {

            $res = $this->resolveCriteria(['slug' => $slug])->firstOrFail();
            return $res;

        }
    }
}
