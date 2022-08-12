<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */
namespace App\Services;

use App\Models\User;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{


    function __construct()
    {
    }

    /**
     * @param $criteria
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    function getList($criteria = [])
    {
        $res = $this->resolveCriteria($criteria)->get();
        return $res;
    }


    protected function resolveCriteria($data = [])
    {
        $query = User::Query();

        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        }

        if (array_key_exists('user_id', $data)) {
            $query = $query->where('id', $data['user_id']);
        }

        if (array_key_exists('name', $data)) {
            $query = $query->where('name', 'LIKE', "'%" . $data['name'] . "%'");
        }
         if (array_key_exists('type', $data)) {
                    $query = $query->where('type', $data['type'] );
         }

        if (array_key_exists('email', $data)) {
            $query = $query->where('email', 'LIKE', "'%" . $data['email'] . "%'");
        }


        if (array_key_exists('created_at', $data)) {
            $query = $query->where('created_at', "LIKE", $data['created_at'] . "%");
        }

        if (array_key_exists('limit', $data) && array_key_exists('offset', $data)) {
            $query = $query->take($data['limit']);
            $query = $query->skip($data['offset']);
        }

        return $query;
    }

    function create($dataIn = [], User &$User)
    {

        $this->mapDataModel($dataIn, $User);

        $User->save();
    }

    public function mapDataModel($data, User &$model)
    {
        $attribute = [
            'name',
            'email',
            'password',
            'type',
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                if($val == 'password')
                {
                    $model->$val = bcrypt($data[$val]);
                }else {
                    $model->$val = $data[$val];
                }
            }
        }
    }

    function update($dataIn = [], User &$User)
    {

        $this->mapDataModel($dataIn, $User);
        $User->save();
    }

    function delete($id)
    {

        $res = $this->getOne($id);

        $res->delete();
    }

    function getOne($id)
    {
        $res = User::findOrFail((int)$id);
        return $res;
    }
}
