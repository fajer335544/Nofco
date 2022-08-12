<?php

namespace App\Http\Controllers\Admin;

use App\Facades\ArticleService;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Facades\ArticleCategoryService;
use Dropbox\Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/**
 * Class ArticlesController
 * @package App\Http\Controllers\Admin
 */
class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */


    private $category;
    private $locale = null;

    public function __construct()
    {
        $this->category = ArticleCategoryService::getOne(Route::current()->getParameter('category_id'));

    }

    public function index($category_id, $locale)
    {

        $dataIn = ['category_id' => $category_id, 'locale' => $locale];
        $results = ArticleService::getList($dataIn);

        if (!is_null($this->category->translation($locale)))
            $pageInfo['page_name'] = $this->category->translation($locale)->name;
        else
            $pageInfo['page_name'] = $this->category->name;

        $pageInfo['title'] = trans('article.admin-articles');

        return view('admin.articles.list', compact('results', 'pageInfo', 'locale', 'category_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $category_id
     * @param $locale
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create($category_id, $locale, $model = null)
    {
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/articles/' . $this->category->id . '/' . $locale);
        if (!is_null($this->category->translation($locale)))
            $pageInfo['page_name'] = $this->category->translation($locale)->name;
        else
            $pageInfo['page_name'] = $this->category->name;

        $pageInfo['title'] = trans('article.admin-articles');
        return view('admin.articles.form', compact('pageInfo', 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ArticleRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArticleRequest $request, $category_id, $locale)
    {
        $maxOrder = (int)ArticleService::getList(['category_id' => $category_id, 'locale' => $locale])->max('record_order');
        $model = New Article();
        $dataIn = $request->all();
        $dataIn['record_order'] = $maxOrder + 1;
        $dataIn['category_id'] = $this->category->id;

        $dataIn['locale'] = $locale;

        $dataIn['user_id'] = Auth::user()->id;
        if (!array_key_exists('visible', $dataIn))
            $dataIn['visible'] = 0;
        try {
            ArticleService::create($dataIn, $model);
        } catch (\Exception $e) {
              //  dd($e);
            return redirect('admin/articles/' . $this->category->id . '/' . $locale . '/create')->withInput()->withErrors(trans("page.create-error"));
        }
        return redirect('admin/articles/' . $this->category->id . '/' . $locale)->with('success', trans('all.success'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($category_id, $locale, $id)
    {
        $model = ArticleService::getOne($id);

        $pageInfo['form_method'] = 'PUT';

        if (!is_null($this->category->translation($locale)))
            $pageInfo['page_name'] = $this->category->translation($locale)->name;
        else
            $pageInfo['page_name'] = $this->category->name;


        $pageInfo['title'] = trans('article.admin-articles');
        $pageInfo['title'] .= ' - ' . $model->name;

        $pageInfo['form_url'] = url('admin/articles/' . $this->category->id . '/' . $locale . '/' . $id);

        return view('admin.articles.form', compact('pageInfo', 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ArticleRequest $request, $category_id, $locale, $id)
    {
        $model = ArticleService::getOne($id);
        $dataIn = $request->all();
        if (!array_key_exists('visible', $dataIn))
            $dataIn['visible'] = 0;
        $dataIn['user_id'] = Auth::user()->id;
        if(array_key_exists('delete-file' , $dataIn))
        {
            if(Storage::disk()->exists($model->file))
                unlink(public_path($model->file));

            $model->file = '';
        }
        if(array_key_exists('delete-image' , $dataIn))
        {
            if(Storage::disk()->exists($model->image))
                unlink(public_path($model->image));

            $model->image = '';
        }
        try {
            ArticleService::update($dataIn, $model);
        } catch (\Exception $e) {
            return redirect('admin/articles/' . $this->category->id . '/' . $locale . '/' . $id . '/edit')->withInput()->withErrors(trans("page.edit-error"));
        }
        return redirect('admin/articles/' . $this->category->id . '/' . $locale)->with('success', trans('all.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $category_id, $locale, $id)
    {
        $model = ArticleService::getOne($id);
        ArticleService::delete($id);
        return redirect('admin/articles/' . $this->category->id . '/' . $locale)->with('success', trans('all.success'));
    }


    public function visibility(Request $request, $category_id, $locale, $id)
    {
        $model = ArticleService::getOne($id);

        if ($model->visible == 1)
            $dataIn['visible'] = 0;
        else
            $dataIn['visible'] = 1;

        try {
            ArticleService::update($dataIn, $model);
            return redirect('admin/articles/' . $this->category->id . '/' . $locale)->with('success', trans('all.success'));

        } catch (Exception $e) {
            return redirect('admin/articles/' . $this->category->id . '/' . $locale)->withInput()->withErrors(trans("page.edit-error"));
        }
    }

    public function sort(Request $request, $category_id, $locale, $id, $direction)
    {
        $model = ArticleService::getOne($id);

        if ($direction == 'up')
            $list = ArticleService::getList(['category_id' => $model->category_id, 'locale' => $model->locale, 'record_order_up' => $model->record_order, 'orderBy' => ['record_order', 'asc']]);
        else
            $list = ArticleService::getList(['category_id' => $model->category_id, 'locale' => $model->locale, 'record_order_down' => $model->record_order, 'orderBy' => ['record_order', 'desc']]);
        if (!$list->isEmpty()) {
            $newModel = $list->first();

            $dataIn['record_order'] = $model->record_order;
            $dataInN['record_order'] = $newModel->record_order;
            ArticleService::update($dataIn, $newModel);
            ArticleService::update($dataInN, $model);

        }

        return redirect('admin/articles/' . $this->category->id . '/' . $locale)->with('success', trans('all.success'));
    }

}
