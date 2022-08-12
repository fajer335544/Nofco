<?php

namespace App\Http\Controllers\Admin;

use App\Facades\BlockCategoryService;
use App\Http\Requests\BlockCategoryRequest;
use App\Models\BlockCategory;
use App\Models\BlockCategoryTranslation;
use App\Facades\BlockCategoryTranslationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class BlockCategoriesController extends Controller
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
        $results = BlockCategoryService::getList(['lang' => App::getLocale()]);
        $pageInfo['page_name'] = trans('block_category.web-pages') ;
        $pageInfo['title'] = trans('block_category.web-pages') ;
        return view( 'admin.block_categories.list' , compact( 'results', 'pageInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create( $model = null)
    {
        $pageInfo['page_name'] = trans('block_category.add-page');
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/blocks/categories');
        $pageInfo['title'] = trans('block_category.web-pages');
        return view('admin.block_categories.form' , compact('categoryId' , 'pageInfo' , 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BlockCategoryRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlockCategoryRequest $request )
    {

        $model = New BlockCategory();
        $dataIn = $request->all();
        $dataIn['user_id'] = Auth::user()->id;
        try {
            BlockCategoryService::create($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['category_id'] = $model->id;
                $translationModels = New BlockCategoryTranslation();
                BlockCategoryTranslationService::create($dataIn[$key], $translationModels);
            }
        }catch (\Exception $e) {
            $model->delete();
            return redirect('admin/blocks/categories/create')->withInput()->withErrors(trans("block_category.create-error"));
        }
        return redirect('admin/blocks/categories')->with('success' , trans('all.success'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = BlockCategoryService::getOne($id);
        $pageInfo['form_method'] = 'PUT';
        $pageInfo['page_name'] = $model->name;
        $pageInfo['title'] = trans('block_category.web-pages');
        $pageInfo['form_url'] = url('admin/blocks/categories/'.$id);
        return view('admin.block_categories.form' , compact( 'pageInfo' , 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BlockCategoryRequest $request, $id)
    {
        $model = BlockCategoryService::getOne($id);

        $dataIn = $request->all();
        $dataIn['user_id'] = Auth::user()->id;
        try {
             BlockCategoryService::update($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['category_id'] = $model->id;
                $translationModels = BlockCategoryTranslationService::getList(['locale'=> $key ,'category_id'=> $model->id]);
                if($translationModels->isEmpty())
                    BlockCategoryTranslationService::create($dataIn[$key], $translationModels);
                else
                    BlockCategoryTranslationService::update($dataIn[$key], $translationModels->first());

            }
        }catch (\Exception $e) {
            return redirect('admin/blocks/categories/'.$id.'/edit')->withInput()->withErrors(trans("block_category.edit-error"));
        }
        return redirect('admin/blocks/categories')->with('success' , trans('all.success'));
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
        $model = BlockCategoryService::delete($id);
        return redirect('admin/blocks/categories')->with('success' , trans('all.success'));
    }
}
