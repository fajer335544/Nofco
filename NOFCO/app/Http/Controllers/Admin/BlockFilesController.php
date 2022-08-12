<?php

namespace App\Http\Controllers\Admin;

use App\Facades\BlockService;
use App\Facades\FileService;
use App\Facades\BlockCategoryService;
use App\Facades\FileTranslationService;
use App\Http\Requests\FileRequest ;

use App\Models\Block;
use App\Models\File;
use App\Models\FileTranslation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class BlockFilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $categoryId
     * @return \Illuminate\Http\Response
     */


    private $category;
    private $block;
    private $tBlock;
    private $type;
    private $refer;
    private $refer_name;
    private $type_array = ['image' => 'البوم الصور' , 'video' => 'فيديو' , 'files'=> 'ملفات'];
    public function __construct()
    {
        $this->category = BlockCategoryService::getOne( Route::current()->getParameter('category_id'));
        $this->block = BlockService::getOne(Route::current()->getParameter('refer_id'));
        $this->tBlock = $this->block->translation(App::getLocale());

        $this->type = Route::current()->getParameter('type');
        if(!array_key_exists($this->type  , $this->type_array))
        {
            $this->type = "image";
        }
        $this->refer = Block::class;
        $this->refer_name  = 'blocks';
        View::share('refer_name' , $this->refer_name);
    }

    public function index($categoryId , $referId, $type )
    {
        $pageInfo['page_name'] =  $this->tBlock->name;
        $pageInfo['page_name'] .= ' - ' . trans('all.'.$this->type.'s');


        $pageInfo['title'] = trans('all.'.$this->type.'s');
        $pageInfo['type'] = $this->type;


        $dataIn['refer_id'] = $this->block->id ;
        $dataIn['refer'] = $this->refer;
        $dataIn['type'] = $this->type;
        $results = FileService::getList($dataIn);


        switch ($this->type) {
            case 'image':
                return view('admin.images.list', compact($this->refer_name ,'categoryId', 'referId', 'pageInfo', 'results'));
            case 'video':
                return view('admin.video.list', compact($this->refer_name,'categoryId', 'referId', 'pageInfo', 'results'));
            case 'files':
                return view('admin.files.list', compact($this->refer_name,'categoryId', 'referId', 'pageInfo', 'results'));

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $categoryId
     * @param $referId
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create($categoryId , $referId , $type,  $model = null)
    {

        $pageInfo['page_name'] =  $this->tBlock->name;
        $pageInfo['page_name'] .= ' - ' . trans('all.'.$this->type.'s');


        $pageInfo['title'] = trans('all.'.$this->type.'s');


        $pageInfo['form_method'] = 'POST';
        $pageInfo['type'] = $this->type;
        $pageInfo['form_url'] = url('admin/blocks/'.$categoryId.'/'.$referId.'/'.$this->type.'/files');

        switch ($this->type) {
            case 'image':
                return view('admin.images.form', compact($this->refer_name , 'categoryId', 'referId','type' ,  'pageInfo', 'model'));
            case 'video':
                return view('admin.video.form', compact( $this->refer_name, 'categoryId', 'referId' ,'type' , 'pageInfo', 'model'));
            case 'files':
                return view('admin.files.form', compact($this->refer_name, 'categoryId', 'referId' ,'type' , 'pageInfo', 'model'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $categoryId
     * @param $referId
     * @param BlockFileRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store( FileRequest  $request  , $categoryId, $referId, $type)
    {
        $dataIn['refer_id'] = $this->block->id ;
        $dataIn['refer'] = $this->refer;
        $dataIn['type'] = $this->type;
        $maxOrder = (int)FileService::getList($dataIn)->max('record_order');
        $model = New File();
        $dataIn = $request->all();
        $dataIn['record_order'] = $maxOrder + 1;
        $dataIn['refer_id'] = $this->block->id ;
        $dataIn['refer'] = $this->refer;
        $dataIn['type'] = $this->type;
        $dataIn['visible'] =1;
        try {
             FileService::create($dataIn, $model);

            foreach (config('app.locales') as $key => $value)
            {
                if(array_key_exists($key , $dataIn)) {
                    $dataIn[$key]['locale'] = $key;
                    $dataIn[$key]['file_id'] = $model->id;
                    $translationModels = New FileTranslation();
                    FileTranslationService::create($dataIn[$key], $translationModels);
                }
            }
        }catch (\Exception $e) {
            if($model)
               $model->delete();
            return redirect('admin/blocks/'.$categoryId.'/'.$referId.'/'.$this->type.'/files/create')->withInput()->withErrors(trans("file.create-error"));
        }
        return redirect('admin/blocks/'.$categoryId.'/'.$referId.'/'.$this->type.'/files')->with('success' , trans('all.success'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $categoryId
     * @param $referId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit( $categoryId , $referId ,$type  , $id)
    {
        $model = FileService::getOne($id);


        $pageInfo['page_name'] =  $this->tBlock->name;
        $pageInfo['page_name'] .= ' - ' . trans('all.'.$this->type.'s');


        $pageInfo['title'] =  trans('all.edit') .' '. $model->name;


        $pageInfo['type'] = $this->type;

        $pageInfo['form_method'] = 'PUT';

        $pageInfo['form_url'] = url('admin/blocks/'.$categoryId.'/'.$this->block->id.'/'.$this->type.'/files/'.$id);
        switch ($this->type) {
            case 'image':
                return view('admin.images.form', compact('categoryId', 'referId', 'pageInfo', 'model'));
            case 'files':
                return view('admin.files.form', compact('categoryId', 'referId', 'pageInfo', 'model'));
            case 'video':
                return view('admin.video.form', compact('categoryId', 'referId', 'pageInfo', 'model'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EditBlockFileRequest|Request $request
     * @param $categoryId
     * @param $referId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(FileRequest $request, $categoryId,   $referId ,$type ,  $id)
    {
        $model = FileService::getOne($id);
        $dataIn = $request->all();
        /*if(!$request->hasFile('image'))
        {
            unset($dataIn['image']);
        }*/
        if(array_key_exists('delete-image' , $dataIn))
        {
            if(Storage::disk()->exists($model->src))
                unlink(public_path($model->src));

            $model->src = '';
        }
        $dataIn['refer_id'] = $this->block->id ;
        $dataIn['refer'] = $this->refer;
        $dataIn['type'] = $this->type;

        try {
             FileService::update($dataIn, $model);
            foreach (config('app.locales') as $key => $value) {
                if (array_key_exists($key, $dataIn)) {
                    $dataIn[$key]['locale'] = $key;
                    $dataIn[$key]['file_id'] = $model->id;
                    $translationModels = FileTranslationService::getList(['locale' => $key, 'file_id' => $model->id]);
                    if ($translationModels->isEmpty()){
                        $translationModels = New FileTranslation();
                        FileTranslationService::create($dataIn[$key], $translationModels);
                    }
                    else
                        FileTranslationService::update($dataIn[$key], $translationModels->first());

                }
            }
        }catch (\Exception $e) {

            return redirect('admin/blocks/'.$categoryId.'/'.$referId.'/'.$this->type.'/files/'.$id.'/edit')->withInput()->withErrors(trans("file.edit-error"));
        }
        return redirect('admin/blocks/'.$categoryId.'/'.$referId.'/'.$this->type.'/files')->with('success' , trans('all.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $categoryId
     * @param $referId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $categoryId,   $referId ,  $type , $id)
    {
        $model = FileService::delete($id);
        return redirect('admin/blocks/'.$categoryId.'/'.$referId.'/'.$this->type.'/files')->with('success' , trans('all.success'));
    }

    public function visibility(Request $request, $categoryId,   $referId ,  $type , $id)
    {
        $model = FileService::getOne($id);

        if ($model->visible == 1)
            $dataIn['visible'] = 0;
        else
            $dataIn['visible'] = 1;

        try {
            FileService::update($dataIn, $model);
            return redirect('admin/blocks/'.$categoryId.'/'.$referId.'/'.$this->type.'/files')->with('success', trans('all.success'));

        } catch (Exception $e) {
            return redirect('admin/blocks/'.$categoryId.'/'.$referId.'/'.$this->type.'/files')->withInput()->withErrors(trans("page.edit-error"));
        }
    }

    public function sort(Request $request, $categoryId,   $referId ,  $type , $id, $direction)
    {
        $model = FileService::getOne($id);

        if ($direction == 'up')
            $list = FileService::getList(['refer_id' => $this->block->id, 'type'=>$this->type,'refer' => $this->refer, 'record_order_up' => $model->record_order, 'orderBy' => ['record_order', 'asc']]);
        else
            $list = FileService::getList(['refer_id' => $this->block->id, 'type'=>$this->type,'refer' => $this->refer, 'record_order_down' => $model->record_order, 'orderBy' => ['record_order', 'desc']]);



        if (!$list->isEmpty()) {
            $newModel = $list->first();

            $dataIn['record_order'] = $model->record_order;
            $dataInN['record_order'] = $newModel->record_order;
            FileService::update($dataIn, $newModel);
            FileService::update($dataInN, $model);

        }

        return redirect('admin/blocks/'.$categoryId.'/'.$referId.'/'.$this->type.'/files')->with('success', trans('all.success'));
    }
}
