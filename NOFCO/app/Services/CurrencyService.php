<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Storage;

/**
 * Class CurrencyService
 * @package App\Services
 */
class CurrencyService
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
        $query = Currency::Query();

        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        }

        if (array_key_exists('id', $data)) {
            $query = $query->where('id', $data['id']);
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

    function create($dataIn = [], Currency &$Currency)
    {
        $this->mapDataModel($dataIn, $Currency);
        $Currency->save();
    }

    public function mapDataModel($data, Currency &$model)
    {
        $attribute = [
            'name',
            'symbol',
            'short_name',
            'user_id'
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }

    function update($dataIn = [], Currency &$Currency)
    {

        $this->mapDataModel($dataIn, $Currency);
        $Currency->save();
    }

    function delete($id)
    {

        $res = $this->getOne($id);

        $res->delete();
    }

    function getOne($id, $slug = '')
    {
        if ($id > 0) {
            $res = Currency::findOrFail($id);
            return $res;
        } else {

            $res = $this->resolveCriteria(['slug' => $slug])->firstOrFail();
            return $res;

        }
    }

    function generateSelect($currencies, $selected = 0)
    {
        $result = '';
        if ($currencies->isNotEmpty())
            foreach ($currencies as $cat) {
                if ($cat->id == $selected)
                    $result .= "<option selected value='$cat->id'>" . $cat->name . "</option>";
                else
                    $result .= "<option  value='$cat->id'>" . $cat->name . "</option>";
            }


        return $result;
    }
}
