<?php

namespace App\Http\Controllers\Admin;

use App\Facades\ArticleService;
use App\Facades\FileService;
use App\Facades\ArticleCategoryService;
use App\Http\Requests\ArticleFileRequest;
use App\Http\Requests\EditArticleFileRequest;

use App\Models\Article;
use App\Models\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class ArticleFilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $categoryId
     * @return \Illuminate\Http\Response
     */


    private $category;
    private $article;
    private $type;
    private $refer;
    private $type_array = ['image' => 'البوم الصور' , 'video' => 'فيديو' , 'files'=> 'ملفات'];
    public function __construct()
    {
        $this->category = ArticleCategoryService::getOne( Route::current()->getParameter('category_id'));
        $this->article = ArticleService::getOne(Route::current()->getParameter('article_id'));
        $this->type = Route::current()->getParameter('type');
        if(!array_key_exists($this->type  , $this->type_array))
        {
            $this->type = "image";
        }
        $this->refer = Article::class;
    }

    public function index($categoryId , $articleId, $type )
    {
        $pageInfo['page_name'] =  $this->article->name;
        $pageInfo['page_name'] .= ' - ' . trans('all.'.$this->type.'s');


        $pageInfo['title'] = trans('all.'.$this->type.'s');
        $pageInfo['type'] = $this->type;


        $dataIn['refer_id'] = $this->article->id ;
        $dataIn['refer'] = $this->refer;
        $dataIn['type'] = $this->type;
        $results = FileService::getList($dataIn);

        switch ($this->type) {
            case 'image':
                return view('admin.articles.images.list', compact('categoryId', 'articleId', 'pageInfo', 'results'));
            case 'video':
                return view('admin.articles.video.list', compact('categoryId', 'articleId', 'pageInfo', 'results'));
            case 'files':
                return view('admin.articles.files.list', compact('categoryId', 'articleId', 'pageInfo', 'results'));

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $categoryId
     * @param $articleId
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create($categoryId , $articleId , $type,  $model = null)
    {

        $pageInfo['page_name'] =  $this->article->name;
        $pageInfo['page_name'] .= ' - ' . trans('all.'.$this->type.'s');


        $pageInfo['title'] = trans('all.'.$this->type.'s');


        $pageInfo['form_method'] = 'POST';
        $pageInfo['type'] = $this->type;
        $pageInfo['form_url'] = url('admin/articles/'.$categoryId.'/'.$articleId.'/'.$this->type.'/files');

        switch ($this->type) {
            case 'image':
                return view('admin.articles.images.form', compact('categoryId', 'articleId','type' ,  'pageInfo', 'model'));
            case 'video':
                return view('admin.articles.video.form', compact('categoryId', 'articleId' ,'type' , 'pageInfo', 'model'));
            case 'files':
                return view('admin.articles.files.form', compact('categoryId', 'articleId' ,'type' , 'pageInfo', 'model'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $categoryId
     * @param $articleId
     * @param ArticleFileRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store( ArticleFileRequest $request  , $categoryId, $articleId, $type)
    {
        $dataIn['refer_id'] = $this->article->id ;
        $dataIn['refer'] = $this->refer;
        $dataIn['type'] = $this->type;
        $maxOrder = (int)FileService::getList($dataIn)->max('record_order');
        $model = New File();
        $dataIn = $request->all();
        $dataIn['record_order'] = $maxOrder + 1;
        $dataIn['refer_id'] = $this->article->id ;
        $dataIn['refer'] = $this->refer;
        $dataIn['type'] = $this->type;
        $dataIn['visible'] =1;
        try {
            $model = FileService::create($dataIn, $model);
        }catch (\Exception $e) {
            dd($e);
            return redirect('admin/articles/'.$categoryId.'/'.$articleId.'/'.$this->type.'/files/create')->withInput()->withErrors(trans("file.create-error"));
        }
        return redirect('admin/articles/'.$categoryId.'/'.$articleId.'/'.$this->type.'/files')->with('success' , trans('all.success'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $categoryId
     * @param $articleId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit( $categoryId , $articleId ,$type  , $id)
    {
        $model = FileService::getOne($id);


        $pageInfo['page_name'] =  $this->article->name;
        $pageInfo['page_name'] .= ' - ' . trans('all.'.$this->type.'s');


        $pageInfo['title'] =  trans('all.edit') .' '. $model->name;


        $pageInfo['type'] = $this->type;

        $pageInfo['form_method'] = 'PUT';

        $pageInfo['form_url'] = url('admin/articles/'.$categoryId.'/'.$this->article->id.'/'.$this->type.'/files/'.$id);
        switch ($this->type) {
            case 'image':
                return view('admin.articles.images.form', compact('categoryId', 'articleId', 'pageInfo', 'model'));
            case 'files':
                return view('admin.articles.files.form', compact('categoryId', 'articleId', 'pageInfo', 'model'));
            case 'video':
                return view('admin.articles.video.form', compact('categoryId', 'articleId', 'pageInfo', 'model'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EditArticleFileRequest|Request $request
     * @param $categoryId
     * @param $articleId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditArticleFileRequest $request, $categoryId,   $articleId ,$type ,  $id)
    {
        $model = FileService::getOne($id);
        $dataIn = $request->all();
        if(!$request->hasFile('image'))
        {
            unset($dataIn['image']);
        }
        $dataIn['refer_id'] = $this->article->id ;
        $dataIn['refer'] = $this->refer;
        $dataIn['type'] = $this->type;

        try {
            $model = FileService::update($dataIn, $model);
        }catch (\Exception $e) {

            return redirect('admin/articles/'.$categoryId.'/'.$articleId.'/'.$this->type.'/files/'.$id.'/edit')->withInput()->withErrors(trans("file.edit-error"));
        }
        return redirect('admin/articles/'.$categoryId.'/'.$articleId.'/'.$this->type.'/files')->with('success' , trans('all.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $categoryId
     * @param $articleId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $categoryId,   $articleId ,  $type , $id)
    {
        FileService::delete($id);
        return redirect('admin/articles/'.$categoryId.'/'.$articleId.'/'.$this->type.'/files')->with('success' , trans('all.success'));
    }

    public function visibility(Request $request, $categoryId,   $articleId ,  $type , $id)
    {
        $model = FileService::getOne($id);

        if ($model->visible == 1)
            $dataIn['visible'] = 0;
        else
            $dataIn['visible'] = 1;

        try {
            FileService::update($dataIn, $model);
            return redirect('admin/articles/'.$categoryId.'/'.$articleId.'/'.$this->type.'/files')->with('success', trans('all.success'));

        } catch (Exception $e) {
            return redirect('admin/articles/'.$categoryId.'/'.$articleId.'/'.$this->type.'/files')->withInput()->withErrors(trans("page.edit-error"));
        }
    }

    public function sort(Request $request, $categoryId,   $articleId ,  $type , $id, $direction)
    {
        $model = FileService::getOne($id);

        if ($direction == 'up')
            $list = FileService::getList(['refer_id' => $this->article->id, 'type'=>$this->type,'refer' => $this->refer, 'record_order_up' => $model->record_order, 'orderBy' => ['record_order', 'asc']]);
        else
            $list = FileService::getList(['refer_id' => $this->article->id, 'type'=>$this->type,'refer' => $this->refer, 'record_order_down' => $model->record_order, 'orderBy' => ['record_order', 'desc']]);
        if (!$list->isEmpty()) {
            $newModel = $list->first();

            $dataIn['record_order'] = $model->record_order;
            $dataInN['record_order'] = $newModel->record_order;
            FileService::update($dataIn, $newModel);
            FileService::update($dataInN, $model);
        }

        return redirect('admin/articles/'.$categoryId.'/'.$articleId.'/'.$this->type.'/files')->with('success', trans('all.success'));
    }
}
