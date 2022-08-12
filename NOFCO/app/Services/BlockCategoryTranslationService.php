<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\BlockCategoryTranslation;
use Illuminate\Support\Facades\Storage;

/**
 * Class BlockCategoryTranslationService
 * @package App\Services
 */
class BlockCategoryTranslationService
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
        $query = BlockCategoryTranslation::Query();

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

    function create($dataIn = [], BlockCategoryTranslation &$BlockCategoryTranslation)
    {

        if (array_key_exists('name', $dataIn))
            $dataIn['slug'] = make_slug($dataIn['name'], '-');
        $this->mapDataModel($dataIn, $BlockCategoryTranslation);
        $BlockCategoryTranslation->save();
    }

    public function mapDataModel($data, BlockCategoryTranslation &$model)
    {
        $attribute = [
            'category_id',
            'name',
            'description',
            'description_one',
            'description_two',
            'description_three',
            'description_four',
            'slug',
            'locale',
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }

    function update($dataIn = [], BlockCategoryTranslation &$BlockCategoryTranslation)
    {
        if (array_key_exists('name', $dataIn))
            $dataIn['slug'] = make_slug($dataIn['name'], '-');
        $this->mapDataModel($dataIn, $BlockCategoryTranslation);
        $BlockCategoryTranslation->save();
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

    function getOne($id, $slug = '')
    {
        if ($id > 0) {
            $res = BlockCategoryTranslation::findOrFail($id);
            return $res;
        } else {

            $res = $this->resolveCriteria(['slug' => $slug])->firstOrFail();
            return $res;

        }
    }
}
