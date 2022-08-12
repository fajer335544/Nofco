<?php

namespace App\Http\Controllers\Admin;

use App\Facades\MenuService;
use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use Dropbox\Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

/**
 * Class MenusController
 * @package App\Http\Controllers\Admin
 */
class MenusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */


    public function __construct()
    {
    }

    public function index($locale = '')
    {
        $dataIn = [];
        if($locale !=  '')
        {
          $dataIn =  ['locale' => $locale];
        }
        $results = MenuService::getList($dataIn);
        $pageInfo['page_name'] = trans('menu.admin-menus');
        $pageInfo['title'] = trans('menu.admin-menus');

        return view('admin.menus.list', compact('results', 'pageInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create($model = null)
    {
        $pageInfo['title'] = trans('menu.add-menu');
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/menus');

        $pageInfo['page_name'] = trans('menu.admin-menus');
        return view('admin.menus.form', compact('pageInfo', 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MenuRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(MenuRequest $request)
    {
        $maxOrder = (int)MenuService::getList()->max('record_order');
        $model = New Menu();
        $dataIn = $request->all();
        $dataIn['record_order'] = $maxOrder + 1;
        if (!array_key_exists('visible', $dataIn))
            $dataIn['visible'] = 0;
        try {
             MenuService::create($dataIn, $model);
        } catch (\Exception $e) {

            return redirect('admin/menus/create')->withInput()->withErrors(trans("page.create-error"));
        }
        return redirect('admin/menus/'.$model->locale)->with('success', trans('all.success'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = MenuService::getOne($id);
        $pageInfo['form_method'] = 'PUT';
        $pageInfo['title'] = $model->name;
        $pageInfo['page_name'] = trans('menu.admin-menus');
        $pageInfo['form_url'] = url('admin/menus/' . $id);

        return view('admin.menus.form', compact('pageInfo', 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(MenuRequest $request, $id)
    {
        $model = MenuService::getOne($id);
        $dataIn = $request->all();
        if (!array_key_exists('visible', $dataIn))
            $dataIn['visible'] = 0;
        try {
             MenuService::update($dataIn, $model);
        } catch (\Exception $e) {
            return redirect('admin/menus/' . $id . '/edit')->withInput()->withErrors(trans("page.edit-error"));
        }
        return redirect('admin/menus/'.$model->locale)->with('success', trans('all.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $model = MenuService::getOne($id);
         MenuService::delete($id);
        return redirect('admin/menus/'.$model->locale)->with('success', trans('all.success'));
    }


    public function visibility(Request $request, $id)
    {
        $model = MenuService::getOne($id);

        if ($model->visible == 1)
            $dataIn['visible'] = 0;
        else
            $dataIn['visible'] = 1;
        try {
             MenuService::update($dataIn, $model);
            return redirect('admin/menus/'.$model->locale)->with('success', trans('all.success'));

        }catch (Exception $e)
        {
            return redirect('admin/menus/'.$model->locale)->withInput()->withErrors(trans("page.edit-error"));
        }
    }

    public function sort(Request $request, $id , $direction)
    {
        $model = MenuService::getOne($id);

        if($direction == 'up')
            $list = MenuService::getList(['locale' => $model->locale, 'record_order_up' => $model->record_order ,  'orderBy' => ['record_order' , 'asc']]);
        else
            $list = MenuService::getList(['locale' => $model->locale, 'record_order_down' => $model->record_order ,  'orderBy' => ['record_order' , 'desc']]);
        if(!$list->isEmpty())
        {
            $newModel = $list->first();
            $dataIn['record_order'] = $model->record_order;
            $dataInN['record_order']= $newModel->record_order;
            MenuService::update($dataIn,$newModel);
            MenuService::update($dataInN,$model);

        }

        return redirect('admin/menus/'.$model->locale)->with('success', trans('all.success'));
    }

}
