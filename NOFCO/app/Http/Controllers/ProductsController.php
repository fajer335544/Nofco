<?php

namespace App\Http\Controllers;

use App\Facades\BrandService;
use App\Facades\ColorService;
use App\Facades\ModelService;
use App\Facades\ProductService;
use App\Facades\ProductCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Chencha\Share\ShareFacade as Share;
use Illuminate\Support\Facades\Route;


class ProductsController extends Controller
{
    /**
     * Create a new controller instance.
     */

    private $category;
    private $tCategory;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->category = ProductCategoryService::getOne(Route::current()->getParameter('category_id'));
            $this->tCategory = $this->category->translation(App::getLocale());
            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     * @param $category_slug
     * @param int $page
     * @return \Illuminate\Http\Response
     */
    public function index($locale, $id, $category_slug)
    {
        $pageInfo['title'] = $this->tCategory->name;
        $pageInfo['breadcrumb'] = ProductCategoryService::generate_urls($this->category);
        $pageInfo['keywords'] = trans('site.home-keywords');
        $pageInfo['description'] = trans('site.description');
        $list = ProductService::getList(['parent' => $this->category->id,'visible'=>1,
             'limit' => 12, 'offset' => 0]);
        switch ($this->category->type) {
            case 'product':
                return view('products.list', compact('pageInfo', 'category', 'list'))->with(['category' => $this->category]);

            case 'service':
                return view('services.list', compact('pageInfo', 'category', 'list'))->with(['category' => $this->category]);

            case 'ticket':
                return view('tickets.list', compact('pageInfo', 'category', 'list'))->with(['category' => $this->category]);
        }
    }

    public function get_ajax_products(Request $request, $locale, $category_id, $offset = 0)
    {
        if ($request->ajax()) {
            try {
                $list = ProductService::getList(['parent' => $this->category->id,'visible'=>1,
                  'limit' => 6, 'offset' => (int)$offset]);
                switch ($this->category->type) {
                    case 'product':
                        $view = (string)view('products.list-ajax', compact('list', 'colNum'));
                        break;
                    case 'service':
                        $view = (string)view('services.list-ajax', compact('list', 'colNum'));
                        break;
                    case 'ticket':
                        $view = (string)view('tickets.list-ajax', compact('list', 'colNum'));
                        break;
                }

                return response()->json([
                    'error' => false,
                    'result' => [
                        'view' => (string)$view,
                        'last_item' => (count($list) == 0 ? true : false)
                    ],
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'error' => true,
                    'result' => $e->getMessage(),
                ]);
            }
        } else {
            abort(403, 'Unauthorized action');
        }
    }


    public function newarrival($locale)
    {
        $pageInfo['title'] = trans('all.newarrival');

        $list = ProductService::getList(['new' => 1,'visible'=>1,
            'limit' => 100, 'offset' => 0]);


                return view('products.new', compact('pageInfo',  'list'));

    }


    public function modelspro($locale,$slug,  $model_id = '', $modelslug)
    {
        $pageInfo['title'] = trans('all.carmodels');

        if ($model_id != 0) {
            $model = ModelService::getOne($model_id);
            $pageInfo['title'] .= ' - ' . $model->translation(App::getLocale())->name ;
        }
        $model_list = ProductService::getList(['model_id' => $model_id, 'limit' => 24, 'offset' => 0]);

        return view('products.model_list', compact('pageInfo', 'model_list', 'model'));
    }



    public function view($locale, $category_id, $category_slug ,$id, $slug)
    {
        $result = ProductService::getOne($id);
        $list = ProductService::getList(['parent' => $this->category->id,
            ])->shuffle()->take(5);
        $colors = ColorService::getList();
        $pageInfo['title'] = $this->tCategory->name . ' - ' . $result->translation(App::getLocale())->name;
        $pageInfo['keywords'] = trans('site.home-keywords');
        $pageInfo['description'] = trans('site.description');

        $pageInfo['breadcrumb'] = ProductCategoryService::generate_urls($this->category);
        $pageInfo['breadcrumb'][] = "<a href='javascript:void(0)' class='active'>".$result->translation(App::getLocale())->name."</a>";

        $pageInfo['share'] = Share::load(url()->current(), $result->translation(App::getLocale())->name)->services('facebook', 'gplus', 'twitter', 'linkedin', 'pinterest');
        switch ($this->category->type) {
            case 'product':
                return view('products.view', compact('pageInfo', 'list', 'category','colors', 'result'));

            case 'service':
                 return view('services.view', compact('pageInfo', 'list', 'category', 'result'));

            case 'ticket':
                return view('ticket.view', compact('pageInfo', 'list', 'category', 'result'));
        }
    }



    public function search(Request $request)
    {
        $result = $request->all();
        $pageInfo['title'] = trans('all.searchtitle');
        if (is_array($result))
            foreach ($result as $key => $value)
                if ($result[$key] == '')
                    unset($result[$key]);

        $result['is_accepted'] = 1;


        $products = ProductService::getList($result);

        $count = count($products);


        return view('products.search', compact('pageInfo', 'products', 'count'));
    }

}
