<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */
namespace App\Services;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Class FileService
 * @package App\Services
 */
class FileService
{
    function __construct()
    {
    }

    protected function resolveCriteria($data = [])
    {
        $query = File::Query();

        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        }

        if (array_key_exists('file_id', $data)) {
            $query = $query->where('id', $data['file_id']);
        }


        if (array_key_exists('name', $data)) {
            $query = $query->where('name', 'LIKE', "'%" . $data['name'] . "%'");
        }


        if (array_key_exists('refer_id', $data)) {
            $query = $query->where('refer_id', $data['refer_id']);
        }


        if (array_key_exists('refer', $data)) {
            $query = $query->where('refer', (string)$data['refer']);
        }

        if (array_key_exists('visible', $data)) {
            $query = $query->where('visible', (string)$data['visible']);
        }

        if (array_key_exists('type', $data)) {
            $query = $query->where('type', "LIKE", $data['type']);
        }
        if (array_key_exists('visible', $data)) {
            $query = $query->where('visible',  $data['visible']);
        }

        if (array_key_exists('created_at', $data)) {
            $query = $query->where('created_at', "LIKE", $data['created_at'] . "%");
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

    function getOne($id)
    {

        $res = File::findOrFail(intval($id));
        return $res;
    }

    /**
     * @param $criteria
     */
    function getList($criteria = [])
    {
        $res = $this->resolveCriteria($criteria)->get();
        return $res;
    }

    function create($dataIn = [], File &$file)
    {

        if (array_key_exists('src', $dataIn)) {
            $dataIn['src'] = $dataIn['src']->store('uploads');
        }


        if (array_key_exists('url', $dataIn)) {
            $dataIn['url'] = get_youtube_id($dataIn['url']);
        }

        $this->mapDataModel($dataIn, $file);

        $file->save();
    }

    function update($dataIn = [], File &$file)
    {
        if (array_key_exists('src', $dataIn)) {
            $dataIn['src'] = $dataIn['src']->store('uploads');
        }


        if (array_key_exists('url', $dataIn)) {
            $dataIn['url'] = get_youtube_id($dataIn['url']);
        }

        $this->mapDataModel($dataIn, $file);
        $file->save();
    }

    function delete($id)
    {

        $res = $this->getOne($id);

        if (Storage::exists($res->src)) {
            Storage::delete($res->src);
        }


        if (Storage::exists($res->file)) {
            Storage::delete($res->file);
        }

        $res->delete();
    }

    public function mapDataModel($data, File &$model)
    {
        $attribute = [
            'visible',
            'refer_id',
            'refer',
            'type',
            'record_order',
            'name',
            'src',
            'url',
            'post_date'
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }
}
