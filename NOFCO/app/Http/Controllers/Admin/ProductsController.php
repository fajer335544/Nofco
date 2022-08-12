<?php

namespace App\Http\Controllers\Admin;

use App\Facades\BrandService;
use App\Facades\ProductCategoryService;
use App\Facades\ProductService;
use App\Facades\ProductTranslationService;
use App\Http\Requests\ProductsRequest;
use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use PragmaRX\Tracker\Vendor\Laravel\Facade as Tracker;


class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $categoryId
     * @return \Illuminate\Http\Response
     */


    private $category;
    private $tCategory;

    public function __construct()
    {
    }

    public function index()
    {
        $results = ProductService::getList();
        $pageInfo['page_name'] = trans('product.admin-page');
        $pageInfo['title'] = trans('product.all-products');

        return view('admin.products.list', compact('results', 'pageInfo'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $categoryId
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create($model = null)
    {

        $pageInfo['page_name'] = trans('product.admin-page');
        $pageInfo['title'] = trans('product.new-products');

        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/products');

        $brandos = BrandService::getList();

        return view('admin.products.form', compact( 'model', 'brandos', 'pageInfo'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $categoryId
     * @param ProductsRequest|Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(ProductsRequest $request)
    {

        $model = New Product();
        $maxOrder = (int)ProductService::getList([])->max('record_order');

        $dataIn = $request->all();

        $dataIn['user_id'] = Auth::id();
        $dataIn['visible'] = 1;
        $dataIn['record_order'] = $maxOrder + 1;
        try {
            ProductService::create($dataIn, $model);
            foreach (config('app.locales') as $key => $value) {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['product_id'] = $model->id;
                $translationModels = New ProductTranslation();
                ProductTranslationService::create($dataIn[$key], $translationModels);
            }
        } catch (\Exception $e) {

            $model->delete();

            return redirect('admin/products/create')->withInput()->withErrors(trans("product.create-error"));
        }
        return redirect('admin/products')->with('success', trans('all.success'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = ProductService::getOne($id);

        $pageInfo['form_method'] = 'PUT';
        $pageInfo['page_name'] = trans('product.admin-page');
        $pageInfo['title'] = $model->name;
        $pageInfo['form_url'] = url('admin/products/' . $id);

        return view('admin.products.form', compact('model', 'pageInfo'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductsRequest $request,  $id)
    {
        $model = ProductService::getOne($id);
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

        $dataIn['user_id'] = Auth::id();
        try {
             ProductService::update($dataIn, $model);

            foreach (config('app.locales') as $key => $value) {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['product_id'] = $model->id;
                $translationModels = ProductTranslationService::getList(['locale' => $key, 'product_id' => $model->id]);
                if ($translationModels->isEmpty()) {
                    $translationModels = New ProductTranslation();
                    ProductTranslationService::create($dataIn[$key], $translationModels);
                }else {
                    ProductTranslationService::update($dataIn[$key], $translationModels->first());
                }
            }

        } catch (\Exception $e) {
            return redirect('admin/products/' . $id . '/edit')->withInput()->withErrors(trans("product.edit-error"));
        }
        return redirect('admin/products')->with('success', trans('all.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,  $id)
    {
        $model = ProductService::delete($id);
        return redirect('admin/products')->with('success', trans('all.success'));
    }


    public function visibility(Request $request,  $id)
    {
        $model = ProductService::getOne($id);
        if ($model->visible == 1)
            $dataIn['visible'] = 0;
        else
            $dataIn['visible'] = 1;

        try {

            ProductService::update($dataIn, $model);

            return redirect('admin/products')->with('success', trans('all.success'));

        } catch (Exception $e) {
            return redirect('admin/products')->withInput()->withErrors(trans("page.edit-error"));
        }
    }


    public function new(Request $request,  $id)
    {
        $model = ProductService::getOne($id);
        if ($model->new == 1)
            $dataIn['new'] = 0;
        else
            $dataIn['new'] = 1;

        try {

            ProductService::update($dataIn, $model);

            return redirect('admin/products')->with('success', trans('all.success'));

        } catch (Exception $e) {
            return redirect('admin/products')->withInput()->withErrors(trans("page.edit-error"));
        }
    }



    public function sort(Request $request,  $id, $direction)
    {
        $model = ProductService::getOne($id);

        if ($direction == 'up')
            $list = ProductService::getList(['record_order_up' => $model->record_order, 'orderBy' => ['record_order', 'asc']]);
        else
            $list = ProductService::getList(['record_order_down' => $model->record_order, 'orderBy' => ['record_order', 'desc']]);
        if (!$list->isEmpty()) {
            $newModel = $list->first();

            $dataIn['record_order'] = $model->record_order;
            $dataInN['record_order'] = $newModel->record_order;
            ProductService::update($dataIn, $newModel);
            ProductService::update($dataInN, $model);

        }


        return redirect('admin/products')->with('success', trans('all.success'));
    }

}
