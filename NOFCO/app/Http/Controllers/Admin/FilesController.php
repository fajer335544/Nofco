<?php

namespace App\Http\Controllers\Admin;

use App\Facades\ArticleService;
use App\Facades\FileService;
use App\Facades\CategoryService;
use App\Http\Requests\ArticleFileRequest;
use App\Http\Requests\EditArticleFileRequest;

use App\Models\Article;
use App\Models\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class FilesController extends Controller
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
    private $refer_type;
    private $type_array = ['image' => 'البوم الصور' , 'episode' => 'الحلقات' , 'occasion'=> 'فيديوهات المناسبة'];
    public function __construct()
    {
        $this->category = CategoryService::getOne( Route::current()->getParameter('category_id'));
        $this->article = ArticleService::getOne(Route::current()->getParameter('article_id'));
        $this->type = Route::current()->getParameter('type');
        if(!array_key_exists($this->type  , $this->type_array))
        {
            $this->type = "image";
        }
        $this->refer_type = Article::class;
    }

    public function index($categoryId , $type, $articleId )
    {
        $dataIn['refer_id'] = $this->article->id ;
        $dataIn['refer_type'] = $this->refer_type;
        $dataIn['type'] = $this->type;
        $results = FileService::getList($dataIn);
        $pageInfo['page_name'] = $this->type_array[$this->type];
        $pageInfo['type'] = $this->type;
        $pageInfo['title'] =  $this->category->name ." - ". $this->article->title;
        switch ($this->type) {
            case 'image':
                return view('admin.files.list', compact('categoryId', 'articleId', 'pageInfo', 'results'));
            case 'occasion':
            case 'episode':
                return view('admin.video.list', compact('categoryId', 'articleId', 'pageInfo', 'results'));
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
    public function create($categoryId , $type,  $articleId ,  $model = null)
    {
        $pageInfo['page_name'] = $this->type_array[$this->type];
        $pageInfo['title'] =  $this->category->name ." - ". $this->article->title;

        $pageInfo['form_method'] = 'POST';
        $pageInfo['type'] = $this->type;
        $pageInfo['form_url'] = url('admin/cats/'.$this->category->id.'/articles/'.$this->type.'/'.$articleId.'/files');

        switch ($this->type) {
            case 'image':
                return view('admin.files.form', compact('categoryId', 'articleId','type' ,  'pageInfo', 'model'));
            case 'occasion':
            case 'episode':
                return view('admin.video.form', compact('categoryId', 'articleId' ,'type' , 'pageInfo', 'model'));
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
    public function store( ArticleFileRequest $request  , $categoryId, $type, $articleId)
    {

        $model = New File();
        $dataIn = $request->all();
        $dataIn['refer_id'] = $this->article->id ;
        $dataIn['refer_type'] = $this->refer_type;
        $dataIn['type'] = $this->type;
        $dataIn['lang'] = App::getLocale();
        try {
            $model = FileService::create($dataIn, $model);
        }catch (\Exception $e) {

            return redirect('admin/cats/'.$this->category->id.'/articles/'.$this->type.'/'.$articleId.'/files/create')->withInput()->withErrors(trans("file.create-error"));
        }
        return redirect('admin/cats/'.$this->category->id.'/articles/'.$this->type.'/'.$articleId.'/files')->with('success' , trans('all.success'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $categoryId
     * @param $articleId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit( $categoryId ,$type , $articleId , $id)
    {
        $model = FileService::getOne($id);

        $pageInfo['title'] =  trans('all.edit') .' '. $model->title;
        $pageInfo['page_name'] =  $this->category->name ." - ". $this->article->title ;
        $pageInfo['form_method'] = 'PUT';
        $pageInfo['form_url'] = url('admin/cats/'.$this->category->id.'/articles/'.$this->type.'/'.$this->article->id.'/files/'.$id);
        switch ($this->type) {
            case 'image':
                return view('admin.files.form', compact('categoryId', 'articleId', 'pageInfo', 'model'));
            case 'occasion':
            case 'episode':
                return view('admin.video.form', compact('categoryId', 'articleId', 'pageInfo', 'model'));
        }    }

    /**
     * Update the specified resource in storage.
     *
     * @param EditArticleFileRequest|Request $request
     * @param $categoryId
     * @param $articleId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditArticleFileRequest $request, $categoryId, $type ,  $articleId ,  $id)
    {
        $model = FileService::getOne($id);
        $dataIn = $request->all();
        if(!$request->hasFile('image'))
        {
            unset($dataIn['image']);
        }
        $dataIn['refer_id'] = $this->article->id ;
        $dataIn['refer_type'] = $this->refer_type;
        $dataIn['type'] = $this->type;

        try {
            $model = FileService::update($dataIn, $model);
        }catch (\Exception $e) {
            dd($e);
            return redirect('admin/cats/'.$categoryId.'/articles/'.$this->type.'/'.$articleId.'/files/'.$id.'/edit')->withInput()->withErrors(trans("file.edit-error"));
        }
        return redirect('admin/cats/'.$categoryId.'/articles/'.$this->type.'/'.$articleId.'/files')->with('success' , trans('all.success'));
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
    public function destroy(Request $request, $categoryId,  $type , $articleId ,  $id)
    {
        $model = FileService::delete($id);
        return redirect('admin/cats/'.$categoryId.'/articles/'.$this->type.'/'.$articleId.'/files')->with('success' , trans('all.success'));
    }
}
