<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */
namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;

/**
 * Class CategoryService
 * @package App\Services
 */
class CategoryService{


    function __construct()
    {
    }

    /**
     * @param $criteria
     */
    function getList($criteria = []){
        $res = $this->resolveCriteria($criteria)->get();
        return $res;
    }

    protected function resolveCriteria($data = [])
    {
        $query = Category::Query();

        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        }

        if (array_key_exists('category_id', $data)) {
            $query = $query->where('id', $data['category_id']);
        }

        if (array_key_exists('slug', $data)) {
            $query = $query->where('slug', "LIKE" ,  $data['slug']);
        }

        if (array_key_exists('lang', $data)) {
            $query = $query->where('lang', $data['lang']);
        }

        if( array_key_exists('type' , $data))
        {
            $query = $query->where('type' , (int) $data['type'] );
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

    function create( $dataIn = [], Category &$Category){
        $dataIn['slug'] = make_slug($dataIn['name'] , '-') ;
        $this->mapDataModel($dataIn , $Category);
        $Category->save();
    }

    public function mapDataModel($data, Category &$model)
    {
        $attribute = [
            'name',
            'slug',
            'keywords',
            'lang',
         ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }

    function update($dataIn = [] , Category &$Category ){

        $dataIn['slug'] = make_slug($dataIn['name'] , '-') ;
        $this->mapDataModel($dataIn , $Category);
        $Category->save();
    }

    function delete($id){

        $res = $this->getOne($id);
        if(Storage::exists($res->image))
        {
            Storage::delete($res->image);
        }
        $res->delete();
    }

    function getOne($id , $slug = ''){
        if($id > 0){
            $res = Category::findOrFail($id);
                return $res;
        }else{

            $res  = $this->resolveCriteria(['slug'=> $slug])->firstOrFail();
            return $res;

        }
    }
}
