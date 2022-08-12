<?php

namespace App\Http\Controllers\Admin;

use App\Facades\BrandService;
use App\Facades\ModelService;
use App\Http\Requests\ModelRequest;
use App\Models\CarModel;
use App\Models\CarModelTranslation;
use App\Facades\ModelTranslationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CarModelsController extends Controller
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
        $results = ModelService::getList(['lang' => App::getLocale()]);
        $pageInfo['page_name'] = trans('carmodels.web-pages') ;
        $pageInfo['title'] = trans('carmodels.web-pages') ;
        return view( 'admin.carmodels.list' , compact( 'results', 'pageInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create( $model = null)
    {
        $pageInfo['page_name'] = trans('carmodels.add-page');
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/carmodels');
        $pageInfo['title'] = trans('carmodels.web-pages');
        $brandos = BrandService::getList();
        return view('admin.carmodels.form' , compact( 'model', 'brandos', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ModelRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ModelRequest $request )
    {

        $model = New CarModel();
        $maxOrder = (int)ModelService::getList()->max('record_order');
        $dataIn = $request->all();
        $dataIn['user_id'] = Auth::user()->id;
        $dataIn['record_order'] = $maxOrder + 1;
        try {
            ModelService::create($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['model_id'] = $model->id;
                $translationModels = New CarModelTranslation();
                ModelTranslationService::create($dataIn[$key], $translationModels);
            }
        }catch (\Exception $e) {

            $model->delete();
            return redirect('admin/carmodels/create')->withInput()->withErrors(trans("carmodels.create-error"));
        }
        return redirect('admin/carmodels')->with('success' , trans('all.success'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = ModelService::getOne($id);
        $pageInfo['form_method'] = 'PUT';
        $pageInfo['page_name'] = $model->name;
        $pageInfo['title'] = trans('carmodels.web-pages');
        $pageInfo['form_url'] = url('admin/carmodels/'.$id);
        $brandos = BrandService::getList();
         return view('admin.carmodels.form' , compact( 'pageInfo' ,'brandos', 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ModelRequest $request, $id)
    {
        $model = ModelService::getOne($id);

        $dataIn = $request->all();
       /* if (!$request->hasFile('image')) {
            unset($dataIn['image']);
        }*/
        if(array_key_exists('delete-image' , $dataIn))
        {
            if(Storage::disk()->exists($model->image))
                unlink(public_path($model->image));

            $model->image = '';
        }
        $dataIn['user_id'] = Auth::user()->id;
        try {
            ModelService::update($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['model_id'] = $model->id;
                $translationModels = ModelTranslationService::getList(['locale'=> $key ,'model_id'=> $model->id]);
                if($translationModels->isEmpty())
                    ModelTranslationService::create($dataIn[$key], $translationModels);
                else
                    ModelTranslationService::update($dataIn[$key], $translationModels->first());

            }
        }catch (\Exception $e)
        {
            return redirect('admin/carmodels/'.$id.'/edit')->withInput()->withErrors(trans("carmodels.edit-error"));
        }
        return redirect('admin/carmodels')->with('success' , trans('all.success'));
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
        $model = ModelService::delete($id);
        return redirect('admin/carmodels')->with('success' , trans('all.success'));
    }

    public function sort(Request $request,  $id, $direction)
    {
        $model = ModelService::getOne($id);

        if ($direction == 'up')
            $list = ModelService::getList([  'record_order_up' => $model->record_order , 'orderBy' => ['record_order' , 'asc']]);
        else
            $list = ModelService::getList(['record_order_down' => $model->record_order,'orderBy' => ['record_order' , 'desc']]);
        if (!$list->isEmpty()) {
            $newModel = $list->first();

            $dataIn['record_order'] = $model->record_order;
            $dataInN['record_order'] = $newModel->record_order;
            ModelService::update($dataIn, $newModel);
            ModelService::update($dataInN, $model);

        }

        return redirect('admin/carmodels')->with('success' , trans('all.success'));
    }
}
