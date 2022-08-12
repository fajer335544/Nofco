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


class ModelsController extends Controller
{
    public function models($locale,  $model_id = '', $modelslug)
    {
        $pageInfo['title'] = trans('all.carmodels');

        if ($model_id != 0) {
            $model = BrandService::getOne($model_id);
            $pageInfo['title'] .= ' - ' . $model->translation(App::getLocale())->name ;
        }
        $models = ModelService::getList(['brand_id' => $model_id, 'limit' => 24, 'offset' => 0]);

        return view('models.model_list', compact('pageInfo', 'models', 'model'));
    }

}
