<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProductCategoryService
 * @package App\Services
 */
class ProductCategoryService
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
        $query = ProductCategory::Query();

        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        }

        if (array_key_exists('id', $data)) {
            $query = $query->where('id', $data['category_id']);
        }


        if (array_key_exists('type', $data)) {
            $query = $query->where('type', $data['type']);
        }

        if (array_key_exists('parent', $data)) {
            $query = $query->where('parent', (int)$data['parent']);
        }

        if (array_key_exists('visible', $data)) {
            $query = $query->where('visible', $data['visible']);
        }

        if (array_key_exists('name', $data)) {
            $query = $query->where('name', 'LIKE', "'%" . $data['name'] . "%'");
        }

        if (array_key_exists('hasSons', $data)) {
            $query = $query->whereHas('suns');
        } if (array_key_exists('notHasSons', $data)) {
        $query = $query->whereNotIn('id' , function ($q){
            $q->select('parent')->from((new ProductCategory())->getTable());
        });
    }

        if (array_key_exists('record_order_up', $data)) {
            $query = $query->where('record_order', '>', $data['record_order_up']);
        }

        if (array_key_exists('record_order_down', $data)) {
            $query = $query->where('record_order', '<', $data['record_order_down']);
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

    function create($dataIn = [], ProductCategory &$ProductCategory)
    {

        if (array_key_exists('image', $dataIn)) {
            $dataIn['image'] = $dataIn['image']->store('uploads');
        }
        $this->mapDataModel($dataIn, $ProductCategory);
        $ProductCategory->save();
    }

    public function mapDataModel($data, ProductCategory &$model)
    {
        $attribute = [
            'record_order',
            'name',
            'description',
            'type',
            'parent',
            'image',
            'user_id',
            'visible',
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }

    function update($dataIn = [], ProductCategory &$ProductCategory)
    {

        if (array_key_exists('image', $dataIn)) {
            $dataIn['image'] = $dataIn['image']->store('uploads');
        }
        $this->mapDataModel($dataIn, $ProductCategory);
        $ProductCategory->save();
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
            $res = ProductCategory::findOrFail($id);
            return $res;
        } else {

            $res = $this->resolveCriteria(['slug' => $slug])->firstOrFail();
            return $res;

        }
    }

    public  function generateSelect($categories, $selected = 0)
    {
        $result = '';
        if ($categories->isNotEmpty())
            foreach ($categories as $cat) {
                if ($cat->suns->isNotEmpty()) {
                    $result .= "<optgroup label='" . $cat->translation(App::getLocale())->name . "'>";
                    $result .= $this->generateSelect($cat->suns, $selected);
                    $result .= "</optgroup>";
                } else {
                    if ($cat->id == $selected)
                        $result .= "<option data-type='" . $cat->type . "' selected value='$cat->id'>" . $cat->translation(App::getLocale())->name . "</option>";
                    else
                        $result .= "<option data-type='" . $cat->type . "'  value='$cat->id'>" . $cat->translation(App::getLocale())->name . "</option>";
                }
            }

        return $result;
    }

   public function generate_urls($category, &$array = [])
    {
        $array[] = "<a href=\"" . url(
                App::getLocale() . '/' .
                make_slug(config('app.product_types')[$category->type])
                . '/' . $category->id . '/' .
                $category->translation(App::getLocale())->slug) .
            "\">"
            . $category->translation(App::getLocale())->name
            . "</a>";
        if ((int) $category->parent == 0)
        {
            return array_reverse($array);
        }
        else {
           return  $this->generate_urls($category->parents, $array);
        }

    }


}
