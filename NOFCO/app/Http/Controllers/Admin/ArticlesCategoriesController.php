<?php

namespace App\Http\Controllers\Admin;

use App\Facades\ArticleCategoryService;
use App\Http\Requests\ArticleCategoryRequest;
use App\Models\ArticleCategory;
use App\Models\ArticleCategoryTranslation;
use App\Facades\ArticleCategoryTranslationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ArticlesCategoriesController extends Controller
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
        $results = ArticleCategoryService::getList(['lang' => App::getLocale()]);
        $pageInfo['page_name'] = trans('category.web-pages') ;
        $pageInfo['title'] = trans('category.web-pages') ;
        return view( 'admin.articles_categories.list' , compact( 'results', 'pageInfo'));
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
        $pageInfo['form_url'] = url('admin/articles/categories');
        $pageInfo['title'] = trans('category.web-pages');
        return view('admin.articles_categories.form' , compact('categoryId' , 'pageInfo' , 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ArticleCategoryRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArticleCategoryRequest $request )
    {
        $model = New ArticleCategory();
        $dataIn = $request->all();
        $dataIn['user_id'] = Auth::user()->id;
        try {
            ArticleCategoryService::create($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['category_id'] = $model->id;
                $translationModels = New ArticleCategoryTranslation();
                ArticleCategoryTranslationService::create($dataIn[$key], $translationModels);
            }
        }catch (\Exception $e) {
            $model->delete();
            return redirect('admin/articles/categories/create')->withInput()->withErrors(trans("category.create-error"));
        }
        return redirect('admin/articles/categories')->with('success' , trans('all.success'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = ArticleCategoryService::getOne($id);
        $pageInfo['form_method'] = 'PUT';
        $pageInfo['page_name'] = $model->name;
        $pageInfo['title'] = trans('category.web-pages');
        $pageInfo['form_url'] = url('admin/articles/categories/'.$id);
        return view('admin.articles_categories.form' , compact( 'pageInfo' , 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ArticleCategoryRequest $request, $id)
    {
        $model = ArticleCategoryService::getOne($id);

        $dataIn = $request->all();
        $dataIn['user_id'] = Auth::user()->id;
        try {
             ArticleCategoryService::update($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['category_id'] = $model->id;
                $translationModels = ArticleCategoryTranslationService::getList(['locale'=> $key ,'category_id'=> $model->id]);
                if($translationModels->isEmpty())
                    ArticleCategoryTranslationService::create($dataIn[$key], $translationModels);
                else
                    ArticleCategoryTranslationService::update($dataIn[$key], $translationModels->first());

            }
        }catch (\Exception $e) {
            return redirect('admin/articles/categories/'.$id.'/edit')->withInput()->withErrors(trans("category.edit-error"));
        }
        return redirect('admin/articles/categories')->with('success' , trans('all.success'));
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
        $model = ArticleCategoryService::delete($id);
        return redirect('admin/articles/categories')->with('success' , trans('all.success'));
    }


}
