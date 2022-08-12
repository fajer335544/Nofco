<?php

namespace App\Http\Controllers\Admin;

use App\Facades\CategoryService;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */


    public function __construct()
    {
    }


    public function index()
    {
        $results = CategoryService::getList(['lang' => App::getLocale()]);
        $pageInfo['page_name'] = trans('category.web-pages') ;
        $pageInfo['title'] = trans('category.web-pages') ;
        return view( 'admin.categories.list' , compact( 'results', 'pageInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create( $model = null)
    {
        $pageInfo['page_name'] = trans('category.add-page');
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/categories');
        $pageInfo['title'] = trans('category.web-pages');
        return view('admin.categories.form' , compact('categoryId' , 'pageInfo' , 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request )
    {

        $model = New Category();
        $dataIn = $request->all();
        $dataIn['lang'] = App::getLocale();
        try {
            $model = CategoryService::create($dataIn, $model);
        }catch (\Exception $e) {
            return redirect('admin/categories/create')->withInput()->withErrors(trans("category.create-error"));
        }
        return redirect('admin/categories')->with('success' , trans('all.success'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = CategoryService::getOne($id);

        $pageInfo['form_method'] = 'PUT';
        $pageInfo['page_name'] = $model->name;
        $pageInfo['title'] = trans('category.web-pages');
        $pageInfo['form_url'] = url('admin/categories/'.$id);

        return view('admin.categories.form' , compact( 'pageInfo' , 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        $model = CategoryService::getOne($id);
        $dataIn = $request->all();

        try {
            $model = CategoryService::update($dataIn, $model);
        }catch (\Exception $e) {
            return redirect('admin/categories/'.$id.'/edit')->withInput()->withErrors(trans("category.edit-error"));
        }
        return redirect('admin/categories')->with('success' , trans('all.success'));
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
        $model = CategoryService::delete($id);
        return redirect('admin/categories')->with('success' , trans('all.success'));
    }
}
