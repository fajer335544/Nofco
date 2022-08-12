<?php

namespace App\Http\Controllers\Admin;

use App\Facades\ColorService;
use App\Http\Requests\ColorRequest;
use App\Models\Color;
use App\Models\ColorTranslation;
use App\Facades\ColorTranslationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ColorsController extends Controller
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
        $results = ColorService::getList(['lang' => App::getLocale()]);
        $pageInfo['page_name'] = trans('colors.web-pages') ;
        $pageInfo['title'] = trans('colors.web-pages') ;
        return view( 'admin.colors.list' , compact( 'results', 'pageInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create( $model = null)
    {
        $pageInfo['page_name'] = trans('colors.add-page');
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/colors');
        $pageInfo['title'] = trans('colors.web-pages');
        return view('admin.colors.form' , compact( 'colorId','pageInfo' , 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ColorRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ColorRequest $request )
    {

        $model = New Color();
        $dataIn = $request->all();
        $dataIn['user_id'] = Auth::user()->id;
        try {
            ColorService::create($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['color_id'] = $model->id;
                $translationModels = New ColorTranslation();
                ColorTranslationService::create($dataIn[$key], $translationModels);
            }
        }catch (\Exception $e) {
            /*dd($e);*/
            $model->delete();
            return redirect('admin/colors/create')->withInput()->withErrors(trans("colors.create-error"));
        }
        return redirect('admin/colors')->with('success' , trans('all.success'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = ColorService::getOne($id);
        $pageInfo['form_method'] = 'PUT';
        $pageInfo['page_name'] = $model->name;
        $pageInfo['title'] = trans('colors.web-pages');
        $pageInfo['form_url'] = url('admin/colors/'.$id);
        return view('admin.colors.form' , compact( 'pageInfo' , 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ColorRequest $request, $id)
    {
        $model = ColorService::getOne($id);

        $dataIn = $request->all();
        if (!$request->hasFile('image')) {
            unset($dataIn['image']);
        }
        if(array_key_exists('delete-image' , $dataIn))
        {
            if(Storage::disk()->exists($model->image))
                unlink(public_path($model->image));

            $model->image = '';
        }

        $dataIn['user_id'] = Auth::user()->id;
        try {
            ColorService::update($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['color_id'] = $model->id;
                $translationModels = ColorTranslationService::getList(['locale'=> $key ,'color_id'=> $model->id]);
                if($translationModels->isEmpty())
                    ColorTranslationService::create($dataIn[$key], $translationModels);
                else
                    ColorTranslationService::update($dataIn[$key], $translationModels->first());

            }
        }catch (\Exception $e) {
            return redirect('admin/colors/'.$id.'/edit')->withInput()->withErrors(trans("colors.edit-error"));
        }
        return redirect('admin/colors')->with('success' , trans('all.success'));
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
        $model = ColorService::delete($id);
        return redirect('admin/colors')->with('success' , trans('all.success'));
    }
}
