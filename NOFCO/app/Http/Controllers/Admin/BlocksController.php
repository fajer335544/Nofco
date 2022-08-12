<?php

namespace App\Http\Controllers\Admin;

use App\Facades\BlockCategoryService;
use App\Facades\BlockService;
use App\Facades\BlockTranslationService;
use App\Facades\CategoryService;
use App\Http\Requests\BlocksRequest;
use App\Models\Block;
use App\Models\BlockTranslation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use PragmaRX\Tracker\Vendor\Laravel\Facade as Tracker;


class BlocksController extends Controller
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
        $this->middleware(function ($request ,  $next) {
            $this->category = BlockCategoryService::getOne(Route::current()->getParameter('category_id'));
            $this->tCategory = $this->category->translation(App::getLocale());
            return $next($request);
        });
    }

    public function index($categoryId)
    {

        $dataIn['category_id'] = $this->category->id;

        $results = BlockService::getList($dataIn);
        $pageInfo['page_name'] = $this->tCategory->name;;
        $pageInfo['title'] = $this->tCategory->name;
        $type = $this->category->type;

        switch ($this->category->type) {
            default:
            case 'blog':
                return view('admin.blocks.blog.list', compact('results','type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'home-slider':
            case 'photo_galleries':
                return view('admin.blocks.photo_galleries.list', compact('results','type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'video_galleries':
                return view('admin.blocks.video_galleries.list', compact('results','type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'persons':
                return view('admin.blocks.persons.list', compact('results','type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'links':
                return view('admin.blocks.links.list', compact('results','type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'files':
                return view('admin.blocks.files.list', compact('results','type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'images':
                return view('admin.blocks.images.list', compact('results','type', 'categoryId', 'model', 'pageInfo'));
                break;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $categoryId
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create($categoryId, $model = null)
    {
        $dataIn['category_id'] = $this->category->id;
        $pageInfo['page_name'] = $this->tCategory->name;
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/blocks/' . $this->category->id . '/contents');

        $pageInfo['title'] = $this->tCategory->name;
        $type = $this->category->type;

        switch ($this->category->type) {
            default:
            case 'blog':
                return view('admin.blocks.blog.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'home-slider':
            case 'photo_galleries':
                return view('admin.blocks.photo_galleries.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'video_galleries':
                return view('admin.blocks.video_galleries.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'persons':
                return view('admin.blocks.persons.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'links':
                return view('admin.blocks.links.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'files':
                return view('admin.blocks.files.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'images':
                return view('admin.blocks.images.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $categoryId
     * @param BlocksRequest|Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(BlocksRequest $request, $categoryId)
    {

        $model = New Block();
        $maxOrder = (int)BlockService::getList(['category_id' => $this->category->id])->max('record_order');

        $dataIn = $request->all();



        $dataIn['user_id'] = Auth::id();
        $dataIn['category_id'] = $this->category->id;
        $dataIn['visible'] = 1;
        $dataIn['record_order'] = $maxOrder + 1;
        try {
             BlockService::create($dataIn, $model);
            foreach (config('app.locales') as $key => $value) {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['block_id'] = $model->id;
                $translationModels = New BlockTranslation();
                BlockTranslationService::create($dataIn[$key], $translationModels);
            }
        } catch (\Exception $e) {
            $model->delete();
            return redirect('admin/blocks/' . $categoryId . '/contents/create')->withInput()->withErrors(trans("block.create-error"));
        }
        return redirect('admin/blocks/' . $categoryId . '/contents')->with('success', trans('all.success'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($categoryId, $id)
    {
        $model = BlockService::getOne($id);

        $pageInfo['form_method'] = 'PUT';
        $dataIn['category_id'] = $this->category->id;
        $pageInfo['page_name'] = $this->tCategory->name;
        $pageInfo['title'] = $this->tCategory->name . " - " . $model->name;
        $pageInfo['form_url'] = url('admin/blocks/' . $this->category->id . '/contents/' . $id);
        $type = $this->category->type;
        switch ($this->category->type) {
            default:
            case 'blog':
                return view('admin.blocks.blog.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'home-slider':
            case 'photo_galleries':
                return view('admin.blocks.photo_galleries.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'video_galleries':
                return view('admin.blocks.video_galleries.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'persons':
                return view('admin.blocks.persons.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'links':
                return view('admin.blocks.links.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
            case 'files':
            return view('admin.blocks.files.form', compact('type', 'categoryId', 'model', 'pageInfo'));
            break;
            case 'images':
                return view('admin.blocks.images.form', compact('type', 'categoryId', 'model', 'pageInfo'));
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(BlocksRequest $request, $categoryId, $id)
    {
        $model = BlockService::getOne($id);
        $dataIn = $request->all();
      /*  if (!$request->hasFile('image')) {
            unset($dataIn['image']);
        }*/
        if(array_key_exists('delete-image' , $dataIn))
        {
            if(Storage::disk()->exists($model->image))
                unlink(public_path($model->image));

            $model->image = '';
        }

        if (array_key_exists('delete-image_one', $dataIn)) {
            if (Storage::disk()->exists($model->image_one))
                unlink(public_path($model->image_one));

            $model->image_one = '';
        }
        if (array_key_exists('delete-image_two', $dataIn)) {
            if (Storage::disk()->exists($model->image_two))
                unlink(public_path($model->image_two));

            $model->image_two = '';
        }
        if (array_key_exists('delete-image_three', $dataIn)) {
            if (Storage::disk()->exists($model->image_three))
                unlink(public_path($model->image_three));

            $model->image_three = '';
        }

        if (array_key_exists('delete-image_four', $dataIn)) {
            if (Storage::disk()->exists($model->image_four))
                unlink(public_path($model->image_four));

            $model->image_four = '';
        }





        $dataIn['user_id'] = Auth::id();
        $dataIn['category_id'] = $this->category->id;
        try {
             BlockService::update($dataIn, $model);

            foreach (config('app.locales') as $key => $value) {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['block_id'] = $model->id;
                $translationModels = BlockTranslationService::getList(['locale' => $key, 'block_id' => $model->id]);
                if ($translationModels->isEmpty())
                    BlockTranslationService::create($dataIn[$key], $translationModels);
                else
                    BlockTranslationService::update($dataIn[$key], $translationModels->first());

            }

        } catch (\Exception $e) {

            return redirect('admin/blocks/' . $categoryId . '/contents/' . $id . '/edit')->withInput()->withErrors(trans("block.edit-error"));
        }
        return redirect('admin/blocks/' . $categoryId . '/contents')->with('success', trans('all.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $categoryId, $id)
    {
        $model = BlockService::delete($id);
        return redirect('admin/blocks/' . $categoryId . '/contents')->with('success', trans('all.success'));
    }


    public function visibility(Request $request, $categoryId, $id)
    {
        $model = BlockService::getOne($id);

        if ($model->visible == 1)
            $dataIn['visible'] = 0;
        else
            $dataIn['visible'] = 1;

        try {
            BlockService::update($dataIn, $model);
            return redirect('admin/blocks/' . $this->category->id . '/contents')->with('success', trans('all.success'));

        } catch (Exception $e) {
            return redirect('admin/blocks/' . $this->category->id . '/contents')->withInput()->withErrors(trans("page.edit-error"));
        }
    }

    public function sort(Request $request, $categoryId, $id, $direction)
    {
        $model = BlockService::getOne($id);

        if ($direction == 'up')
            $list = BlockService::getList(['category_id' => $model->category_id, 'record_order_up' => $model->record_order, 'orderBy' => ['record_order', 'asc']]);
        else
            $list = BlockService::getList(['category_id' => $model->category_id, 'record_order_down' => $model->record_order, 'orderBy' => ['record_order', 'desc']]);
        if (!$list->isEmpty()) {
            $newModel = $list->first();

            $dataIn['record_order'] = $model->record_order;
            $dataInN['record_order'] = $newModel->record_order;
            BlockService::update($dataIn, $newModel);
            BlockService::update($dataInN, $model);

        }


        return redirect('admin/blocks/' . $this->category->id . '/contents')->with('success', trans('all.success'));
    }

}
