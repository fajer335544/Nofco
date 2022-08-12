<?php

namespace App\Http\Controllers\Admin;

use App\Facades\EventCategoryService;
use App\Facades\EventService;
use App\Facades\EventTranslationService;
use App\Facades\CategoryService;
use App\Http\Requests\EventsRequest;
use App\Models\Event;
use App\Models\EventTranslation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use PragmaRX\Tracker\Vendor\Laravel\Facade as Tracker;


class EventsController extends Controller
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
        $this->category = EventCategoryService::getOne(Route::current()->getParameter('category_id'));
        $this->tCategory = $this->category->translation(App::getLocale());
    }

    public function index($categoryId)
    {

        $dataIn['category_id'] = $this->category->id;

        $results = EventService::getList($dataIn);
        $pageInfo['page_name'] = $this->tCategory->name;;
        $pageInfo['title'] = $this->tCategory->name;

        return view('admin.events.list', compact('results', 'categoryId', 'model', 'pageInfo'));

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
        $pageInfo['form_url'] = url('admin/events/' . $this->category->id . '/contents');

        $pageInfo['title'] = $this->tCategory->name;


        return view('admin.events.form', compact('categoryId', 'model', 'pageInfo'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $categoryId
     * @param EventsRequest|Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(EventsRequest $request, $categoryId)
    {

        $model = New Event();
        $maxOrder = (int)EventService::getList(['category_id' => $this->category->id])->max('record_order');

        $dataIn = $request->all();

        $dataIn['user_id'] = Auth::id();
        $dataIn['category_id'] = $this->category->id;
        $dataIn['visible'] = 1;
        $dataIn['record_order'] = $maxOrder + 1;
        try {
            EventService::create($dataIn, $model);
            foreach (config('app.locales') as $key => $value) {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['event_id'] = $model->id;
                $translationModels = New EventTranslation();
                EventTranslationService::create($dataIn[$key], $translationModels);
            }
        } catch (\Exception $e) {
            $model->delete();
            return redirect('admin/events/' . $categoryId . '/contents/create')->withInput()->withErrors(trans("event.create-error"));
        }
        return redirect('admin/events/' . $categoryId . '/contents')->with('success', trans('all.success'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($categoryId, $id)
    {
        $model = EventService::getOne($id);

        $pageInfo['form_method'] = 'PUT';
        $dataIn['category_id'] = $this->category->id;
        $pageInfo['page_name'] = $this->tCategory->name;
        $pageInfo['title'] = $this->tCategory->name . " - " . $model->name;
        $pageInfo['form_url'] = url('admin/events/' . $this->category->id . '/contents/' . $id);

        return view('admin.events.form', compact('categoryId', 'model', 'pageInfo'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(EventsRequest $request, $categoryId, $id)
    {
        $model = EventService::getOne($id);
        $dataIn = $request->all();
        if (!$request->hasFile('image')) {
            unset($dataIn['image']);
        }
        $dataIn['user_id'] = Auth::id();
        $dataIn['category_id'] = $this->category->id;
        try {
             EventService::update($dataIn, $model);

            foreach (config('app.locales') as $key => $value) {
                $dataIn[$key]['locale'] = $key;
                $dataIn[$key]['event_id'] = $model->id;
                $translationModels = EventTranslationService::getList(['locale' => $key, 'event_id' => $model->id]);
                if ($translationModels->isEmpty())
                    EventTranslationService::create($dataIn[$key], $translationModels);
                else
                    EventTranslationService::update($dataIn[$key], $translationModels->first());

            }

        } catch (\Exception $e) {

            return redirect('admin/events/' . $categoryId . '/contents/' . $id . '/edit')->withInput()->withErrors(trans("event.edit-error"));
        }
        return redirect('admin/events/' . $categoryId . '/contents')->with('success', trans('all.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $categoryId, $id)
    {
        $model = EventService::delete($id);
        return redirect('admin/events/' . $categoryId . '/contents')->with('success', trans('all.success'));
    }


    public function visibility(Request $request, $categoryId, $id)
    {
        $model = EventService::getOne($id);

        if ($model->visible == 1)
            $dataIn['visible'] = 0;
        else
            $dataIn['visible'] = 1;

        try {
            EventService::update($dataIn, $model);
            return redirect('admin/events/' . $this->category->id . '/contents')->with('success', trans('all.success'));

        } catch (Exception $e) {
            return redirect('admin/events/' . $this->category->id . '/contents')->withInput()->withErrors(trans("page.edit-error"));
        }
    }

    public function sort(Request $request, $categoryId, $id, $direction)
    {
        $model = EventService::getOne($id);

        if ($direction == 'up')
            $list = EventService::getList(['category_id' => $model->category_id, 'record_order_up' => $model->record_order, 'orderBy' => ['record_order', 'asc']]);
        else
            $list = EventService::getList(['category_id' => $model->category_id, 'record_order_down' => $model->record_order, 'orderBy' => ['record_order', 'desc']]);
        if (!$list->isEmpty()) {
            $newModel = $list->first();

            $dataIn['record_order'] = $model->record_order;
            $dataInN['record_order'] = $newModel->record_order;
            EventService::update($dataIn, $newModel);
            EventService::update($dataInN, $model);

        }


        return redirect('admin/events/' . $this->category->id . '/contents')->with('success', trans('all.success'));
    }

}
