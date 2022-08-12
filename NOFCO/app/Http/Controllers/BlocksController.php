<?php

namespace App\Http\Controllers;

use App\Facades\BlockService;
use App\Facades\BlockCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Chencha\Share\ShareFacade as Share;
use Illuminate\Support\Facades\Route;


class BlocksController extends Controller
{
    /**
     * Create a new controller instance.
     */

    private $category;
    private $tCategory;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->category = BlockCategoryService::getOne(Route::current()->getParameter('category_id'));
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
        $pageInfo['breadcrumb'] = BlockCategoryService::generate_urls($this->category);
        $pageInfo['keywords'] = trans('site.home-keywords');
        $pageInfo['description'] = trans('site.description');
        $list = BlockService::getList(['category_id' => $this->category->id, 'visible' => 1,
            'limit' => 12, 'offset' => 0]);
        switch ($this->category->type) {
            case 'persons':
                return view('blocks.persons.list', compact('pageInfo', 'category', 'list'))->with(['category' => $this->category]);

            case 'links':
                return view('blocks.links.list', compact('pageInfo', 'category', 'list'))->with(['category' => $this->category]);

            case 'blog':
                return view('blocks.blogs.list', compact('pageInfo', 'category', 'list'))->with(['category' => $this->category]);
            case 'files':
                return view('blocks.files.list', compact('pageInfo', 'category', 'list'))->with(['category' => $this->category]);
            case 'photo_galleries':
                return view('blocks.galleries.list', compact('pageInfo', 'category', 'list'))->with(['category' => $this->category]);
            case 'video_galleries':
                return view('blocks.videos.list', compact('pageInfo', 'category', 'list'))->with(['category' => $this->category]);
            case 'images':
                return view('blocks.images.list', compact('pageInfo', 'category', 'list'))->with(['category' => $this->category]);
        }
    }

    public function get_ajax_list(Request $request, $locale, $category_id, $offset = 0)
    {
        if ($request->ajax()) {
            try {
                $list = BlockService::getList(['category_id' => $this->category->id, 'visible' => 1,
                    'limit' => 6, 'offset' => (int)$offset]);
                switch ($this->category->type) {
                    case 'persons':
                        $view = (string)view('blocks.persons.list-ajax', compact('list', 'colNum'));
                        break;
                    case 'links':
                        $view = (string)view('blocks.links.list-ajax', compact('list', 'colNum'));
                        break;
                    case 'blog':
                        $view = (string)view('blocks.blogs.list-ajax', compact('list', 'colNum'));
                        break;
                    case 'files':
                        $view = (string)view('blocks.files.list-ajax', compact('list', 'colNum'));
                        break;
                    case 'photo_galleries':
                        $view = (string)view('blocks.galleries.list-ajax', compact('list', 'colNum'));
                        break;
                    case 'video_galleries':
                        $view = (string)view('blocks.videos.list-ajax', compact('list', 'colNum'));
                        break;
                    case 'images':
                        $view = (string)view('blocks.images.list-ajax', compact('list', 'colNum'));
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

    public function view($locale, $category_id, $category_slug, $id, $slug)
    {
        $result = BlockService::getOne($id);
        $list = BlockService::getList(['category_id' => $this->category->id,
        ])->shuffle()->take(5);
        $pageInfo['title'] = $this->tCategory->name . ' - ' . $result->translation(App::getLocale())->name;
        $pageInfo['keywords'] = trans('site.home-keywords');
        $pageInfo['description'] = trans('site.description');

        $pageInfo['breadcrumb'] = BlockCategoryService::generate_urls($this->category);
        $pageInfo['breadcrumb'][] = "<a href='javascript:void(0)' class='active'>" . $result->translation(App::getLocale())->name . "</a>";

        $pageInfo['share'] = Share::load(url()->current(), $result->translation(App::getLocale())->name)->services('facebook', 'gplus', 'twitter', 'linkedin', 'pinterest');
        switch ($this->category->type) {
            case 'persons':
                abort('404');
            case 'links':
                abort('404');
            case 'blog':
                return view('blocks.blogs.view', compact('pageInfo', 'result', 'category', 'list'))->with(['category' => $this->category]);
            case 'files':
                abort('404');
            case 'photo_galleries':
                abort('404');
            case 'video_galleries':
                abort('404');
            case 'images':
                abort('404');
        }
    }

}
