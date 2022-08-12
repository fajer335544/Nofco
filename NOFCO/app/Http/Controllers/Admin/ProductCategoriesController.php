<?php

namespace App\Http\Controllers\Admin;

use App\Facades\ProductCategoryService;
use App\Facades\ProductService;
use App\Http\Requests\ProductCategoryRequest;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use App\Facades\ProductCategoryTranslationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ProductCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    private $parent = null;

    public function __construct()
    {
        if (intval(Route::current()->getParameter('parent')) > 0) {
            $this->parent = ProductCategoryService::getOne(Route::current()->getParameter('parent'));
        }
    }


    public function index($parent_id = 0)
    {
        $results = ProductCategoryService::getList(['parent'=> $parent_id]);
        $pageInfo['page_name'] = trans('product_category.web-pages') ;

        if (!is_null($this->parent))
            $pageInfo['page_name'] .=  ' - ' .$this->parent->name;

        $pageInfo['title'] = trans('product_category.web-pages') ;
        return view( 'admin.product_categories.list' , compact( 'results', 'parent_id','pageInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     *
     * @param null $parent_id
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create( $parent_id = 0 , $model = null)
    {
        $pageInfo['page_name'] = trans('product_category.add-page');
        if (!is_null($this->parent))
            $pageInfo['page_name'] .=  ' - ' .$this->parent->name;
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/products/categories/'.$parent_id);
        $pageInfo['title'] = trans('product_category.web-pages');
        return view('admin.product_categories.form' , compact('categoryId' , 'parent_id', 'pageInfo' , 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductCategoryRequest|Request $request
     * @param int $parent_id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(ProductCategoryRequest $request , $parent_id = 0 )
    {
        $maxOrder = (int)ProductCategoryService::getList([ 'parent' => $parent_id])->max('record_order');

        $model = New ProductCategory();
        $dataIn = $request->all();
        $dataIn['parent'] = $parent_id;
        $dataIn['user_id'] = Auth::user()->id;
        $dataIn['record_order'] = $maxOrder+1;
        try {
            ProductCategoryService::create($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['category_id'] = $model->id;
                $translationModels = New ProductCategoryTranslation();
                ProductCategoryTranslationService::create($dataIn[$key], $translationModels);
            }
        }catch (\Exception $e) {
            $model->delete();
            return redirect('admin/products/categories/'.$parent_id.'/create')->withInput()->withErrors(trans("product_category.create-error"));
        }
        return redirect('admin/products/categories/'.$parent_id)->with('success' , trans('all.success'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $parent_id
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($parent_id = 0, $id )
    {
        $model = ProductCategoryService::getOne($id);
        $pageInfo['form_method'] = 'PUT';
        $pageInfo['page_name'] = $model->name;
        if (!is_null($this->parent))
            $pageInfo['page_name'] .=  ' - ' .$this->parent->name;
        $pageInfo['title'] = trans('product_category.web-pages');
        $pageInfo['form_url'] = url('admin/products/categories/'.$parent_id.'/'.$id);
        return view('admin.product_categories.form' , compact( 'pageInfo' , 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductCategoryRequest $request
     * @param int $parent_id
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductCategoryRequest $request, $parent_id = 0 , $id)
    {
        $model = ProductCategoryService::getOne($id);

        $dataIn = $request->all();
        $dataIn['user_id'] = Auth::user()->id;
        try {
             ProductCategoryService::update($dataIn, $model);
            foreach (config('app.locales') as $key => $value)
            {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['category_id'] = $model->id;
                $translationModels = ProductCategoryTranslationService::getList(['locale'=> $key ,'category_id'=> $model->id]);
                if($translationModels->isEmpty()) {
                    $translationModels = New ProductCategoryTranslation();
                    ProductCategoryTranslationService::create($dataIn[$key], $translationModels);
                }else
                    ProductCategoryTranslationService::update($dataIn[$key], $translationModels->first());

            }
        }catch (\Exception $e) {
            return redirect('admin/products/categories/'.$parent_id.'/'.$id.'/edit')->withInput()->withErrors(trans("product_category.edit-error"));
        }
        return redirect('admin/products/categories/'.$parent_id)->with('success' , trans('all.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $parent_id
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$parent_id = 0, $id)
    {
        $model = ProductCategoryService::delete($id);
        return redirect('admin/products/categories/'.$parent_id)->with('success' , trans('all.success'));
    }

    public function visibility(Request $request, $parent_id = 0, $id)
    {
        $model = ProductCategoryService::getOne($id);
        if ($model->visible == 1)
            $dataIn['visible'] = 0;
        else
            $dataIn['visible'] = 1;

        try {

            ProductCategoryService::update($dataIn, $model);

            return redirect('admin/products/categories/'.$parent_id)->with('success', trans('all.success'));

        } catch (Exception $e) {
            return redirect('admin/products/categories/'.$parent_id)->withInput()->withErrors(trans("page.edit-error"));
        }
    }

    public function sort(Request $request, $parent_id = 0 , $id, $direction)
    {
        $model = ProductCategoryService::getOne($id);

        if ($direction == 'up')
            $list = ProductCategoryService::getList([ 'parent'=> $model->parent, 'record_order_up' => $model->record_order , 'orderBy' => ['record_order' , 'asc']]);
        else
            $list = ProductCategoryService::getList(['parent'=> $model->parent, 'record_order_down' => $model->record_order,'orderBy' => ['record_order' , 'desc']]);
        if (!$list->isEmpty()) {
            $newModel = $list->first();

            $dataIn['record_order'] = $model->record_order;
            $dataInN['record_order'] = $newModel->record_order;
            ProductCategoryService::update($dataIn, $newModel);
            ProductCategoryService::update($dataInN, $model);

        }

         return redirect('admin/products/categories/'.$parent_id)->with('success' , trans('all.success'));
    }
}
