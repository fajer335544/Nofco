<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */
namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Storage;

/**
 * Class ArticleService
 * @package App\Services
 */
class ArticleService
{


    function __construct()
    {
    }

    protected function resolveCriteria($data = [])
    {
        $query = Article::Query();
        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        } else {
            $query = $query->select("*");
        }

        if (array_key_exists('article_id', $data)) {
            $query = $query->where('id', $data['article_id']);
        }

        if (array_key_exists('slug', $data)) {
            $query = $query->where('articles.slug', $data['slug']);
        }

        if (array_key_exists('keyword', $data)) {

            $query = $query->where('articles.name', 'LIKE', "%" . $data['keyword'] . "%");
            $query = $query->orWhere('articles.description', 'LIKE', "%" . $data['keyword'] . "%");
        }

        if (array_key_exists('category_id', $data)) {
            $query = $query->where('category_id', $data['category_id']);
        };

        if (array_key_exists('tags', $data)) {
            $query = $query->Where('articles.tags', 'LIKE', "%" . $data['tags'] . "%");
        };

        if (array_key_exists('keywords', $data)) {
            $query = $query->Where('articles.keywords', 'LIKE', "%" . $data['keywords'] . "%");
        };


        if (array_key_exists('created_by', $data)) {
            $query = $query->where('articles.created_by', $data['created_by']);
        }

        if (array_key_exists('created_at', $data)) {
            $query = $query->where('articles.created_at', "LIKE", "%" . $data['created_at'] . "%");
        }
        if (array_key_exists('visible', $data)) {
            $query = $query->where('visible', $data['visible']);
        }

        if (array_key_exists('locale', $data)) {
            $query = $query->where('articles.locale', $data['locale']);
        }

        if (array_key_exists('post_date', $data)) {
            $query = $query->where('post_date', "<=", $data['post_date']);
        }

        if (array_key_exists('not_in_cat', $data)) {
            $query = $query->whereNotIn('category_id', $data['not_in_cat']);
        }

        if (array_key_exists('in_cat', $data)) {
            $query = $query->whereIn('category_id', $data['in_cat']);
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

    function getOne($id, $slug = '')
    {
        if ($id > 0) {
            $res = Article::findOrFail($id);
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

    function create($dataIn = [], Article &$article)
    {
        $dataIn['slug'] = make_slug($dataIn['name'], '-');
        if (array_key_exists('image', $dataIn)) {
            $dataIn['image'] = $dataIn['image']->store('uploads');
        }

        $this->mapDataModel($dataIn, $article);
        $article->save();
    }

    function update($dataIn = [], Article &$article)
    {
        if (array_key_exists('name', $dataIn)) {
            $dataIn['slug'] = make_slug($dataIn['name'], '-');
        }

        if (array_key_exists('image', $dataIn)) {
            $dataIn['image'] = $dataIn['image']->store('uploads');
        }

        $this->mapDataModel($dataIn, $article);
        $article->save();
    }

        public function delete($id)
    {

        $res = $this->getOne($id);

        if (Storage::exists($res->image)) {
            Storage::delete($res->image);
        }

        $res->delete();
    }

    public function mapDataModel($data, Article &$model)
    {
        $attribute = [
            'category_id'
            ,'locale'
            ,'visible'
            ,'record_order'
            ,'slug'
            ,'name'
            ,'author'
            ,'description'
            ,'tags'
            ,'keywords'
            ,'image'
            ,'file'
            ,'user_id'
            ,'post_date'
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }
}
