<?php

namespace App\Http\Controllers\Admin;

use App\Facades\EventCategoryService;
use App\Http\Requests\EventCategoryRequest;
use App\Models\EventCategory;
use App\Models\EventCategoryTranslation;
use App\Facades\EventCategoryTranslationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class EventCategoriesController extends Controller
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
        $results = EventCategoryService::getList(['lang' => App::getLocale()]);
        $pageInfo['page_name'] = trans('event_category.web-pages') ;
        $pageInfo['title'] = trans('event_category.web-pages') ;
        return view( 'admin.event_categories.list' , compact( 'results', 'pageInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create( $model = null)
    {
        $pageInfo['page_name'] = trans('event_category.add-page');
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/events/categories');
        $pageInfo['title'] = trans('event_category.web-pages');
        return view('admin.event_categories.form' , compact('categoryId' , 'pageInfo' , 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EventCategoryRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventCategoryRequest $request )
    {

        $model = New EventCategory();
        $dataIn = $request->all();
        $dataIn['user_id'] = Auth::user()->id;
        try {
            EventCategoryService::create($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['category_id'] = $model->id;
                $translationModels = New EventCategoryTranslation();
                EventCategoryTranslationService::create($dataIn[$key], $translationModels);
            }
        }catch (\Exception $e) {
            $model->delete();

            return redirect('admin/events/categories/create')->withInput()->withErrors(trans("event_category.create-error"));
        }
        return redirect('admin/events/categories')->with('success' , trans('all.success'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = EventCategoryService::getOne($id);
        $pageInfo['form_method'] = 'PUT';
        $pageInfo['page_name'] = $model->name;
        $pageInfo['title'] = trans('event_category.web-pages');
        $pageInfo['form_url'] = url('admin/events/categories/'.$id);
        return view('admin.event_categories.form' , compact( 'pageInfo' , 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EventCategoryRequest $request, $id)
    {
        $model = EventCategoryService::getOne($id);

        $dataIn = $request->all();
        $dataIn['user_id'] = Auth::user()->id;
        try {
             EventCategoryService::update($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['category_id'] = $model->id;
                $translationModels = EventCategoryTranslationService::getList(['locale'=> $key ,'category_id'=> $model->id]);
                if($translationModels->isEmpty())
                    EventCategoryTranslationService::create($dataIn[$key], $translationModels);
                else
                    EventCategoryTranslationService::update($dataIn[$key], $translationModels->first());

            }
        }catch (\Exception $e) {
            return redirect('admin/events/categories/'.$id.'/edit')->withInput()->withErrors(trans("event_category.edit-error"));
        }
        return redirect('admin/events/categories')->with('success' , trans('all.success'));
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
        $model = EventCategoryService::delete($id);
        return redirect('admin/events/categories')->with('success' , trans('all.success'));
    }
}
