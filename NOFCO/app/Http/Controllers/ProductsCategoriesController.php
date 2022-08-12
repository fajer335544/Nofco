<?php

namespace App\Http\Controllers;

use App\Facades\ProductService;
use App\Facades\ProductCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Chencha\Share\ShareFacade as Share;
use Illuminate\Support\Facades\Route;


class ProductsCategoriesController extends Controller
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
    public function index($locale)
    {
        $pageInfo['title'] = trans('all.bodystyle');
        /*$pageInfo['breadcrumb'] = trans('all.products');*/
        $pageInfo['keywords'] = trans('site.home-keywords');
        $pageInfo['description'] = trans('site.description');
        $list = ProductCategoryService::getList(['visible'=>1,
             'limit' => 24, 'offset' => 0]);
        return view('products_categories.list', compact('pageInfo',  'list'));
    }



}
