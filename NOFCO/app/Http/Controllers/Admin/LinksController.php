<?php

namespace App\Http\Controllers\Admin;

use App\Facades\LinkService;
use App\Facades\MenuService;
use App\Http\Requests\CreateLinkRequest;
use App\Http\Requests\LinkRequest;
use App\Models\Link;
use App\Models\User;
use Dropbox\Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/**
 * Class LinksController
 * @package App\Http\Controllers\Admin
 */
class LinksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */


    private $menu;
    private $parent = null;

    public function __construct()
    {
        $this->menu = MenuService::getOne(Route::current()->getParameter('menu_id'));
        if (intval(Route::current()->getParameter('parent_id')) > 0) {
            $this->parent = LinkService::getOne(Route::current()->getParameter('parent_id'));
        }
    }

    public function index($menu_id, $parent_id = 0)
    {

        $dataIn = ['menu_id' => $menu_id, 'parent' => $parent_id];
        $results = LinkService::getList($dataIn);
        $pageInfo['page_name'] = $this->menu->name;
        if (!is_null($this->parent))
            $pageInfo['page_name'] .= ' - ' . $this->parent->name;

        $pageInfo['title'] = trans('link.admin-links');

        return view('admin.links.list', compact('results', 'pageInfo', 'parent_id', 'menu_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $menu_id
     * @param $parent_id
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create($menu_id, $parent_id = 0, $model = null)
    {
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/links/' . $this->menu->id . '/' . $parent_id);
        $pageInfo['page_name'] = $this->menu->name;
        if (!is_null($this->parent))
            $pageInfo['page_name'] .= ' - ' . $this->parent->name;

        $pageInfo['title'] = trans('link.admin-links');
        return view('admin.links.form', compact('pageInfo', 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LinkRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateLinkRequest $request, $menu_id, $parent_id = 0)
    {
        $maxOrder = (int)LinkService::getList(['menu_id' => $menu_id, 'parent' => $parent_id])->max('record_order');
        $model = New Link();
        $dataIn = $request->all();
        $dataIn['record_order'] = $maxOrder + 1;
        $dataIn['menu_id'] = $this->menu->id;

        if (!is_null($this->parent))
            $dataIn['parent'] = $this->parent->id;

        $dataIn['locale'] = $this->menu->locale;
        $dataIn['user_id'] = Auth::user()->id;
        if (!array_key_exists('visible', $dataIn))
            $dataIn['visible'] = 0;
        try {
            LinkService::create($dataIn, $model);
        } catch (\Exception $e) {

            return redirect('admin/links/' . $this->menu->id . '/' . $parent_id . '/create')->withInput()->withErrors(trans("page.create-error"));
        }
        return redirect('admin/links/' . $this->menu->id . '/' . $parent_id)->with('success', trans('all.success'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($menu_id, $parent_id = 0, $id)
    {
        $model = LinkService::getOne($id);
        $pageInfo['form_method'] = 'PUT';


        $pageInfo['page_name'] = $this->menu->name;
        if (!is_null($this->parent))
            $pageInfo['page_name'] .= ' - ' . $this->parent->name;

        $pageInfo['title'] = trans('link.admin-links');
        $pageInfo['title'] .= ' - ' . $model->name;
        $pageInfo['form_url'] = url('admin/links/' . $this->menu->id . '/' . $parent_id . '/' . $id);

        return view('admin.links.form', compact('pageInfo', 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(LinkRequest $request, $menu_id, $parent_id = 0, $id)
    {
        $model = LinkService::getOne($id);
        $dataIn = $request->all();
        if (Auth::user()->can('useAdministration', User::class)) {
            if (!array_key_exists('visible', $dataIn))
                $dataIn['visible'] = 0;
        }
        if (array_key_exists('delete-file', $dataIn)) {
            if (Storage::disk()->exists($model->file))
                unlink(public_path($model->file));

            $model->file = '';
        }
        if (array_key_exists('delete-home_image', $dataIn)) {
            if (Storage::disk()->exists($model->home_image))
                unlink(public_path($model->home_image));

            $model->home_image = '';
        }

        if (array_key_exists('delete-image_one', $dataIn)) {
            if (Storage::disk()->exists($model->image_one))
                unlink(public_path($model->image_one));

            $model->image_one = '';
        }
        if (array_key_exists('delete-image_two', $dataIn)) {
            if (Storage::disk()->exists($model->image_two))
                unlink(public_path($model->image_two));

            $model->image_two = '';
        }
        if (array_key_exists('delete-image_three', $dataIn)) {
            if (Storage::disk()->exists($model->image_three))
                unlink(public_path($model->image_three));

            $model->image_three = '';
        }

        if (array_key_exists('delete-image_four', $dataIn)) {
            if (Storage::disk()->exists($model->image_four))
                unlink(public_path($model->image_four));

            $model->image_four = '';
        }


        if (array_key_exists('delete-header_image', $dataIn)) {
            if (Storage::disk()->exists($model->header_image))
                unlink(public_path($model->header_image));

            $model->header_image = '';
        }

        $dataIn['user_id'] = Auth::user()->id;
        try {
            LinkService::update($dataIn, $model);
        } catch (\Exception $e) {
            return redirect('admin/links/' . $this->menu->id . '/' . $parent_id . '/' . $id . '/edit')->withInput()->withErrors(trans("page.edit-error"));
        }
        if (Auth::user()->can('useAdministration', User::class))
            return redirect('admin/links/' . $this->menu->id . '/' . $parent_id)->with('success', trans('all.success'));
        else
            return redirect('admin/links/' . $this->menu->id . '/' . $parent_id . '/' . $id . '/edit')->with('success', trans('all.success'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $menu_id, $parent_id = 0, $id)
    {
        $model = LinkService::getOne($id);
        LinkService::delete($id);
        return redirect('admin/links/' . $this->menu->id . '/' . $parent_id)->with('success', trans('all.success'));
    }


    public function visibility(Request $request, $menu_id, $parent_id = 0, $id)
    {
        $model = LinkService::getOne($id);

        if ($model->visible == 1)
            $dataIn['visible'] = 0;
        else
            $dataIn['visible'] = 1;

        try {
            LinkService::update($dataIn, $model);
            return redirect('admin/links/' . $this->menu->id . '/' . $parent_id)->with('success', trans('all.success'));

        } catch (Exception $e) {
            return redirect('admin/links/' . $this->menu->id . '/' . $parent_id)->withInput()->withErrors(trans("page.edit-error"));
        }
    }

    public function sort(Request $request, $menu_id, $parent_id = 0, $id, $direction)
    {
        $model = LinkService::getOne($id);

        if ($direction == 'up')
            $list = LinkService::getList(['menu_id' => $model->menu_id, 'parent' => $model->parent, 'record_order_up' => $model->record_order, 'orderBy' => ['record_order', 'asc']]);
        else
            $list = LinkService::getList(['menu_id' => $model->menu_id, 'parent' => $model->parent, 'record_order_down' => $model->record_order, 'orderBy' => ['record_order', 'desc']]);
        if (!$list->isEmpty()) {
            $newModel = $list->first();

            $dataIn['record_order'] = $model->record_order;
            $dataInN['record_order'] = $newModel->record_order;
            LinkService::update($dataIn, $newModel);
            LinkService::update($dataInN, $model);

        }

        return redirect('admin/links/' . $this->menu->id . '/' . $parent_id)->with('success', trans('all.success'));
    }

}
