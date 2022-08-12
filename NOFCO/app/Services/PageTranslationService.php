<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\PageTranslation;
use Illuminate\Support\Facades\Storage;

/**
 * Class PageTranslationService
 * @package App\Services
 */
class PageTranslationService
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
        $query = PageTranslation::Query();

        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        }

        if (array_key_exists('id', $data)) {
            $query = $query->where('id', $data['id']);
        }

        if (array_key_exists('page_id', $data)) {
            $query = $query->where('page_id', $data['page_id']);
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

    function create($dataIn = [], PageTranslation &$PageTranslation)
    {

        if (array_key_exists('name', $dataIn))
            $dataIn['slug'] = make_slug($dataIn['name'], '-');
        $this->mapDataModel($dataIn, $PageTranslation);
        $PageTranslation->save();
    }

    public function mapDataModel($data, PageTranslation &$model)
    {
        $attribute = [
            'page_id',
            'name',
            'description',
            'comments',
            'slug',
            'locale',
            'link1',
            'link2',
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }

    function update($dataIn = [], PageTranslation &$PageTranslation)
    {
        if (array_key_exists('name', $dataIn))
            $dataIn['slug'] = make_slug($dataIn['name'], '-');
        $this->mapDataModel($dataIn, $PageTranslation);
        $PageTranslation->save();
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
            $res = PageTranslation::findOrFail($id);
            return $res;
        } else {

            $res = $this->resolveCriteria(['slug' => $slug])->firstOrFail();
            return $res;

        }
    }
}
