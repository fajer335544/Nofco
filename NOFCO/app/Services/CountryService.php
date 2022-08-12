<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

/**
 * Class CountryService
 * @package App\Services
 */
class CountryService
{


    function __construct()
    {
    }

    protected function resolveCriteria($data = [])
    {
        $query = Country::Query();
        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        } else {
            $query = $query->select("*");
        }

        if (array_key_exists('id', $data)) {
            $query = $query->where('id', $data['id']);
        }

        if (array_key_exists('en_name', $data)) {
            $query = $query->where('en_name', $data['en_name']);
        }

        if (array_key_exists('po_name', $data)) {
            $query = $query->where('po_name', $data['po_name']);
        }

        if (array_key_exists('symbol', $data)) {
            $query = $query->where('symbol', $data['symbol']);
        }

       /* if (array_key_exists('post_date', $data)) {
            $query = $query->where('post_date', $data['post_date']);
        }

        if (array_key_exists('visible', $data)) {
            $query = $query->where('visible', $data['visible']);
        }*/

        /*if (array_key_exists('category_id', $data)) {
            $query = $query->where('category_id', $data['category_id']);
        }
        if (array_key_exists('parent', $data)) {
            $query = $query->where(function ($query) use ($data) {
                $query->whereRaw('category_id = ' . $data['parent'] .
                    ' or category_id in (select id from product_categories where parent=' . $data['parent'] . ') ');
            });
        }*/





        return $query;
    }

    function getOne($id, $slug = '')
    {
        if ($id > 0) {
            $res = Country::findOrFail($id);
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

    function create($dataIn = [], Country &$model)
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

    function update($dataIn = [], Country &$model)
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

    public function mapDataModel($data, Country &$model)
    {
        $attribute = [

            'en_name',
            'po_name',
            'symbol',
            'image',
            'file',

        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                if($data[$val] !== '') {
                    $model->$val = $data[$val];
                }
            }
        }
    }

    public  function generateSelect($countries, $selected = 0)
    {
        $result = '';
        if ($countries->isNotEmpty())
            foreach ($countries as $cat) {

                if ($cat->id == $selected)
                    $result .= "<option  selected value='$cat->id'>" . $cat->en_name . "</option>";
                else
                    $result .= "<option   value='$cat->id'>" . $cat->en_name . "</option>";

            }

        return $result;
    }
}
