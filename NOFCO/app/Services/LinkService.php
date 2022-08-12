<?php
/**
 * Created by PhpStorm.
 * User: amro
 * Date: 12/3/16
 * Time: 12:03 PM
 */

namespace App\Services;

use App\Models\Link;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

/**
 * Class LinkService
 * @package App\Services
 */
class LinkService
{


    function __construct()
    {
    }

    protected function resolveCriteria($data = [])
    {
        $query = Link::Query();
        if (array_key_exists('columns', $data)) {
            $query = $query->select($data['columns']);
        } else {
            $query = $query->select("*");
        }

        if (array_key_exists('id', $data)) {
            $query = $query->where('id', $data['id']);
        }

        if (array_key_exists('name', $data)) {
            $query = $query->where('links.name', $data['slug']);
        }

        if (array_key_exists('refer', $data)) {
            $query = $query->where('links.refer', $data['refer']);
        }

        if (array_key_exists('locale', $data)) {
            $query = $query->where('links.locale', $data['locale']);
        }

        if (array_key_exists('visible', $data)) {
            $query = $query->where('links.visible', $data['visible']);
        }
        if (array_key_exists('type', $data)) {
            $query = $query->where('links.type', $data['type']);
        }

        if (array_key_exists('menu_id', $data)) {
            $query = $query->where('links.menu_id', $data['menu_id']);
        }
        if (array_key_exists('parent', $data)) {
            $query = $query->where('links.parent', $data['parent']);
        }
        if (array_key_exists('user_id', $data)) {
            $query = $query->where('links.user_id', $data['user_id']);
        }

        if (array_key_exists('record_order_up', $data)) {
            $query = $query->where('links.record_order', '>', $data['record_order_up']);
        }

        if (array_key_exists('record_order_down', $data)) {
            $query = $query->where('links.record_order', '<', $data['record_order_down']);
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
            $res = Link::findOrFail($id);
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

    function create($dataIn = [], Link &$model)
    {

        $dataIn['slug'] = make_slug($dataIn['name'], '-');
        if (array_key_exists('home_image', $dataIn)) {
            $dataIn['home_image'] = $dataIn['home_image']->store('uploads');
        }
        if (array_key_exists('image_one', $dataIn)) {
            $dataIn['image_one'] = $dataIn['image_one']->store('uploads');
        }
        if (array_key_exists('image_two', $dataIn)) {
            $dataIn['image_two'] = $dataIn['image_two']->store('uploads');
        }
        if (array_key_exists('image_three', $dataIn)) {
            $dataIn['image_three'] = $dataIn['image_three']->store('uploads');
        }

        if (array_key_exists('image_four', $dataIn)) {
            $dataIn['image_four'] = $dataIn['image_four']->store('uploads');
        }

        if (array_key_exists('header_image', $dataIn)) {
            $dataIn['header_image'] = $dataIn['header_image']->store('uploads');
        }


        if (array_key_exists('file', $dataIn)) {
            $dataIn['file'] = $dataIn['file']->store('uploads');
        }
        $this->mapDataModel($dataIn, $model);
        $model->save();
    }

    function update($dataIn = [], Link &$model)
    {
        if (array_key_exists('name', $dataIn))
            $dataIn['slug'] = make_slug($dataIn['name'], '-');
        if (array_key_exists('home_image', $dataIn)) {
            $dataIn['home_image'] = $dataIn['home_image']->store('uploads');
        }
        if (array_key_exists('image_one', $dataIn)) {
            $dataIn['image_one'] = $dataIn['image_one']->store('uploads');
        }
        if (array_key_exists('image_two', $dataIn)) {
            $dataIn['image_two'] = $dataIn['image_two']->store('uploads');
        }
        if (array_key_exists('image_three', $dataIn)) {
            $dataIn['image_three'] = $dataIn['image_three']->store('uploads');
        }

        if (array_key_exists('image_four', $dataIn)) {
            $dataIn['image_four'] = $dataIn['image_four']->store('uploads');
        }

        if (array_key_exists('header_image', $dataIn)) {
            $dataIn['header_image'] = $dataIn['header_image']->store('uploads');
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
        if (Storage::exists($res->home_image)) {
            Storage::delete($res->home_image);
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

        if (Storage::exists($res->header_image)) {
            Storage::delete($res->header_image);
        }


        if (Storage::exists($res->file)) {
            Storage::delete($res->file);
        }
        $res->delete();
    }

    public function mapDataModel($data, Link &$model)
    {
        $attribute = [
            'parent',
            'menu_id',
            'locale',
            'visible',
            'name',
            'slug',
            'home_description',
            'description_one',
            'description_two',
            'description_three',
            'description_four',
            'record_order',
            'type',
            'home_image',
            'image_one',
            'image_two',
            'image_three',
            'image_four',
            'header_image',
            'file',
            'included_file',
            'url',
            'target',
            'refer',
            'user_id',
        ];

        foreach ($attribute as $val) {
            if (array_key_exists($val, $data)) {
                $model->$val = $data[$val];
            }
        }
    }

    public function get_url(Link $link)
    {
        switch ($link->type) {
            case "page":
                return "<a href='" . url(App::getLocale() . '/pages/' . $link->id . '/' . $link->slug) . "'>" . $link->name . '</a>';
            case "drop-menu":
                return "<a href='javascript:void(0);' class='dropdown-toggle' data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\"/>" . $link->name . '</a>';
            case "include-menu":
                return "<a href='javascript:void(0);' class='dropdown-toggle' data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\"/>" . $link->name . '</a>';
            case "internal-link":
                return "<a href='" . url(App::getLocale() . $link->url) . "'/>" . $link->name . '</a>';
            case "external-link":
                return "<a href='" . $link->url . "' target='_blank' />" . $link->name . '</a>';
            case "download-file":
                return "<a href='" . url(Storage::disk('uploads')->url($link->file)) . "' target='_blank' />" . $link->name . '</a>';
        }
    }

    public function get_url_path(Link $link)
    {
        switch ($link->type) {
            default:
            case "page":
                return url(App::getLocale() . '/pages/' . $link->id . '/' . $link->slug);
            case "drop-menu":
                return "javascript:void(0);";
            case "internal-link":
                return url(App::getLocale() . $link->url);
            case "external-link":
                return $link->url;
            case "download-file":
                return url(Storage::disk('uploads')->url($link->file));
        }
    }

    public function get_dropdown($link)
    {
        $links = $this->getList(['parent' => $link->id, 'visible' => 1]);
        $cls = ($links->isNotEmpty() && $links->count() > 5) ? "large" : "";
        $results = '<ul class="dropdown-menu ' . $cls . '">';
        if ($links->isNotEmpty()) {
            foreach ($links as $val)
                if ($val->type != 'drop-menu') {
                    $results .= '<li class="dropdown-item">';
                    $results .= $this->get_url($val);
                    $results .= '</li>';
                }
        }

        $results .= '</ul>';
        return $results;
    }

    public function get_footer_dropdown($link)
    {
        $links = $this->getList(['parent' => $link->id, 'visible' => 1]);
        $results = '<ul class="footer-drop">';
        if ($links->isNotEmpty()) {
            foreach ($links as $val)
                if ($val->type != 'drop-menu') {
                    $results .= '<li class="dropdown-item">';
                    $results .= $this->get_url($val);
                    $results .= '</li>';
                }
        }
        $results .= '</ul>';
        return $results;
    }


}
