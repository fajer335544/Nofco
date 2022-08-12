<?php

namespace App\Http\Controllers\Admin;

use App\Facades\ArticleService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Tracker\Vendor\Laravel\Facade as Tracker;

class DashboardController extends Controller
{
    function __construct()
    {


    }

    function index()
    {
        $sessions7 = Tracker::sessions(60 * 24 * 7);
        $sessions30 = Tracker::sessions(60 * 24 * 30) ;
        $sessions360 = Tracker::sessions(60 * 24 * 360) ;
        $usersDevice = $sessions360->groupBy('device.kind');
        $usersBrowser = $sessions360->groupBy('agent.browser');

     //   $articles = ArticleService::getList(['lang' => App::getLocale()]);

//        $collection = collect([]);
//        foreach ($articles as $article) {


//            $articleViews = Tracker::logByRouteName("/articles/{id}/{slug}")->where(function($query ) use($article){
//                $query
//                    ->where('parameter', 'id')
//                    ->where('value', $article->id);
//            })->count();
//            $article->viewCount  = $articleViews;
//            $collection->push($article);
//        }
//        $collection = $collection->sortByDesc('viewCount');

        $pageInfo['page_name'] = trans('all.dashboard');
        $pageInfo['title'] =  trans('all.overview');
        return view('admin.dashboard.index' , compact('pageInfo' , 'collection' , 'sessions7' ,
                                                      'sessions30' , 'sessions360' , 'usersDevice'
                                                      ,'usersBrowser'));

    }

}
