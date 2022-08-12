<?php

namespace App\Http\Controllers\Admin;

use App\Facades\BlockService;
use App\Facades\BrandService;
use App\Facades\ProductCategoryService;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use App\Models\BrandTranslation;
use App\Facades\BrandTranslationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BrandsController extends Controller
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
        $results = BrandService::getList(['lang' => App::getLocale()]);
        $pageInfo['page_name'] = trans('brands.web-pages') ;
        $pageInfo['title'] = trans('brands.web-pages') ;
        return view( 'admin.brands.list' , compact( 'results', 'pageInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create( $model = null)
    {
        $pageInfo['page_name'] = trans('brands.add-page');
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/brands');
        $pageInfo['title'] = trans('brands.web-pages');
        return view('admin.brands.form' , compact( 'brandId','pageInfo' , 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BrandRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrandRequest $request )
    {

        $model = New Brand();
        $maxOrder = (int)BrandService::getList()->max('record_order');
        $dataIn = $request->all();
        $dataIn['user_id'] = Auth::user()->id;
        $dataIn['record_order'] = $maxOrder + 1;

        try {
            BrandService::create($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['brand_id'] = $model->id;
                $translationModels = New BrandTranslation();
                BrandTranslationService::create($dataIn[$key], $translationModels);
            }
        }catch (\Exception $e) {
            dd($e);
            $model->delete();
            return redirect('admin/brands/create')->withInput()->withErrors(trans("brands.create-error"));
        }
        return redirect('admin/brands')->with('success' , trans('all.success'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = BrandService::getOne($id);
        $pageInfo['form_method'] = 'PUT';
        $pageInfo['page_name'] = $model->name;
        $pageInfo['title'] = trans('brands.web-pages');
        $pageInfo['form_url'] = url('admin/brands/'.$id);
        return view('admin.brands.form' , compact( 'pageInfo' , 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BrandRequest $request, $id)
    {
        $model = BrandService::getOne($id);

        $dataIn = $request->all();
        /*if (!$request->hasFile('image')) {
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
            BrandService::update($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['brand_id'] = $model->id;
                $translationModels = BrandTranslationService::getList(['locale'=> $key ,'brand_id'=> $model->id]);
                if($translationModels->isEmpty())
                    BrandTranslationService::create($dataIn[$key], $translationModels);
                else
                    BrandTranslationService::update($dataIn[$key], $translationModels->first());

            }
        }catch (\Exception $e) {
            return redirect('admin/brands/'.$id.'/edit')->withInput()->withErrors(trans("brands.edit-error"));
        }
        return redirect('admin/brands')->with('success' , trans('all.success'));
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
        $model = BrandService::delete($id);
        return redirect('admin/brands')->with('success' , trans('all.success'));
    }

    public function sort(Request $request,  $id, $direction)
    {
        $model = BrandService::getOne($id);

        if ($direction == 'up')
            $list = BrandService::getList([  'record_order_up' => $model->record_order , 'orderBy' => ['record_order' , 'asc']]);
        else
            $list = BrandService::getList(['record_order_down' => $model->record_order,'orderBy' => ['record_order' , 'desc']]);
        if (!$list->isEmpty()) {
            $newModel = $list->first();

            $dataIn['record_order'] = $model->record_order;
            $dataInN['record_order'] = $newModel->record_order;
            BrandService::update($dataIn, $newModel);
            BrandService::update($dataInN, $model);

        }

        return redirect('admin/brands')->with('success' , trans('all.success'));
    }
}
