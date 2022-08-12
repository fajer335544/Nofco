<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\ColorTranslation;
use Illuminate\Support\Facades\Storage;

/**
 * Class ColorTranslationService
 * @package App\Services
 */
class ColorTranslationService
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
        $query = ColorTranslation::Query();

        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        }

        if (array_key_exists('id', $data)) {
            $query = $query->where('id', $data['id']);
        }

        if (array_key_exists('color_id', $data)) {
            $query = $query->where('color_id', $data['color_id']);
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

    function create($dataIn = [], ColorTranslation &$ColorTranslation)
    {

        if (array_key_exists('name', $dataIn))
            $dataIn['slug'] = make_slug($dataIn['name'], '-');
        $this->mapDataModel($dataIn, $ColorTranslation);
        $ColorTranslation->save();
    }

    public function mapDataModel($data, ColorTranslation &$model)
    {
        $attribute = [
            'color_id',
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

    function update($dataIn = [], ColorTranslation &$ColorTranslation)
    {
        if (array_key_exists('name', $dataIn))
            $dataIn['slug'] = make_slug($dataIn['name'], '-');
        $this->mapDataModel($dataIn, $ColorTranslation);
        $ColorTranslation->save();
    }

    function delete($id)
    {

        $res = $this->getOne($id);

        $res->delete();
    }

    function getOne($id, $slug = '')
    {
        if ($id > 0) {
            $res = ColorTranslation::findOrFail($id);
            return $res;
        } else {

            $res = $this->resolveCriteria(['slug' => $slug])->firstOrFail();
            return $res;

        }
    }
}
