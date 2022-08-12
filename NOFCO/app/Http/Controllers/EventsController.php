<?php

namespace App\Http\Controllers;

use App\Facades\EventService;
use App\Facades\EventCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Chencha\Share\ShareFacade as Share;
use Illuminate\Support\Facades\Route;


class EventsController extends Controller
{
    /**
     * Create a new controller instance.
     */

    private $category;
    private $tCategory;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->category = EventCategoryService::getOne(Route::current()->getParameter('category_id'));
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
        $pageInfo['breadcrumb'] = EventCategoryService::generate_urls($this->category);
        $pageInfo['keywords'] = trans('site.home-keywords');
        $pageInfo['description'] = trans('site.description');
        $list = EventService::getList(['category_id' => $this->category->id, 'visible'=>1,
            'limit' => 12, 'offset' => 0]);

        return view('events.list', compact('pageInfo', 'category', 'list'))->with(['category' => $this->category]);

    }

    public function get_ajax_list(Request $request, $locale, $category_id, $offset = 0)
    {
        if ($request->ajax()) {
            try {
                $list = EventService::getList(['category_id' => $this->category->id,'visible'=>1,
                    'limit' => 6, 'offset' => (int)$offset]);

                $view = (string)view('events.list-ajax', compact('list', 'colNum'));
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

    public function view($locale, $category_id, $category_slug, $id, $slug)
    {
        $result = EventService::getOne($id);
        $list = EventService::getList(['category_id' => $this->category->id,
        ])->shuffle()->take(5);
        $pageInfo['title'] = $this->tCategory->name . ' - ' . $result->translation(App::getLocale())->name;
        $pageInfo['keywords'] = trans('site.home-keywords');
        $pageInfo['description'] = trans('site.description');

        $pageInfo['breadcrumb'] = EventCategoryService::generate_urls($this->category);
        $pageInfo['breadcrumb'][] = "<a href='javascript:void(0)' class='active'>" . $result->translation(App::getLocale())->name . "</a>";

        $pageInfo['share'] = Share::load(url()->current(), $result->name)->services('facebook', 'gplus', 'twitter', 'linkedin', 'pinterest');

        return view('events.view', compact('pageInfo', 'result', 'category', 'list'))->with(['category' => $this->category]);

    }

}
