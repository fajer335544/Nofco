<?php

namespace App\Http\Controllers;

use App\Facades\BrandService;
use App\Facades\ModelService;
use App\Facades\ProductService;
use App\Facades\ProductCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Chencha\Share\ShareFacade as Share;
use Illuminate\Support\Facades\Route;


class BrandsController extends Controller
{
    public function index($locale, $brand_slug)
    {
        $pageInfo['title'] = trans('all.brands');

        $pageInfo['keywords'] = trans('site.home-keywords');
        $pageInfo['description'] = trans('site.description');
        $brands = BrandService::getList(['limit' => 100, 'offset' => 0]);

        return view('brands.brand_list', compact('pageInfo', 'brands'));


    }

}
