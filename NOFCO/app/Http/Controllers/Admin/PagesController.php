<?php

namespace App\Http\Controllers\Admin;

use App\Facades\PageService;
use App\Http\Requests\PageRequest;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Facades\PageTranslationService;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class PagesController extends Controller
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
        $results = PageService::getList();
        $pageInfo['page_name'] = trans('page.web-pages') ;
        $pageInfo['title'] = trans('page.web-pages') ;
        return view( 'admin.pages.list' , compact( 'results', 'pageInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create( $model = null)
    {
           $pageInfo['page_name'] = trans('page.add-page');
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/pages');
        $pageInfo['title'] = trans('page.web-pages');
        return view('admin.pages.form' , compact('categoryId' , 'pageInfo' , 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PageRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PageRequest $request )
    {

        $model = New Page();
        $dataIn = $request->all();
        $dataIn['user_id'] = Auth::user()->id;
        try {
            PageService::create($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['page_id'] = $model->id;
                $translationModels = New PageTranslation();
                PageTranslationService::create($dataIn[$key], $translationModels);
            }
        }catch (\Exception $e) {
            dump($e);
            $model->delete();
            die();
            return redirect('admin/pages/create')->withInput()->withErrors(trans("page.create-error"));
        }
        return redirect('admin/pages')->with('success' , trans('all.success'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = PageService::getOne($id);
        $pageInfo['form_method'] = 'PUT';
        $pageInfo['page_name'] = $model->name;
        $pageInfo['title'] = trans('page.web-pages');
        $pageInfo['form_url'] = url('admin/pages/'.$id);
        return view('admin.pages.form' , compact( 'pageInfo' , 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PageRequest $request, $id)
    {
        $model = PageService::getOne($id);

        $dataIn = $request->all();
        $dataIn['user_id'] = Auth::user()->id;
        try {
             PageService::update($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['page_id'] = $model->id;
                $translationModels = PageTranslationService::getList(['locale'=> $key ,'page_id'=> $model->id]);
                if($translationModels->isEmpty())
                    PageTranslationService::create($dataIn[$key], $translationModels);
                else
                    PageTranslationService::update($dataIn[$key], $translationModels->first());

            }
        }catch (\Exception $e) {
            return redirect('admin/pages/'.$id.'/edit')->withInput()->withErrors(trans("page.edit-error"));
        }
        if(Auth::user()->can('useAdministration', User::class))
            return redirect('admin/pages')->with('success' , trans('all.success'));
        else
            return redirect('admin/pages/'.$id.'/edit')->with('success' , trans('all.success'));
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
        $model = PageService::delete($id);
        return redirect('admin/pages')->with('success' , trans('all.success'));
    }
}
