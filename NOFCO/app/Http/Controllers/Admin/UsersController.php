<?php

namespace App\Http\Controllers\Admin;

use App\Facades\UserService;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

/**
 * Class UsersController
 * @package App\Http\Controllers\Admin
 */
class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */


    public function __construct()
    {
    }

    public function index()
    {
        $results = UserService::getList(['lang' => App::getLocale()]);
        $pageInfo['page_name'] = trans('user.admin-users');
        $pageInfo['title'] = trans('user.admin-users');

        return view('admin.users.list', compact('results', 'pageInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $model
     * @return \Illuminate\Http\Response
     */
    public function create($model = null)
    {
        $pageInfo['title'] = trans('user.add-user');
        $pageInfo['form_method'] = 'POST';
        $pageInfo['form_url'] = url('admin/users');

        $pageInfo['page_name'] = trans('user.admin-users');
        return view('admin.users.form', compact('pageInfo', 'model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {

        $model = New User();
        $dataIn = $request->all();
        try {
            $model = UserService::create($dataIn, $model);
        } catch (\Exception $e) {
            return redirect('admin/users/create')->withInput()->withErrors(trans("page.create-error"));
        }
        return redirect('admin/users')->with('success', trans('all.success'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = UserService::getOne($id);
        if (Auth::user()->type == 1 || Auth::user()->id == $model->id) {

            $pageInfo['form_method'] = 'PUT';
            $pageInfo['title'] = $model->name;
            $pageInfo['page_name'] = trans('user.admin-users');
            $pageInfo['form_url'] = url('admin/users/' . $id);

            return view('admin.users.form', compact('pageInfo', 'model'));
        } else
            abort(403, 'Unauthorized Action');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $model = UserService::getOne($id);

        if (Auth::user()->type == 1 || Auth::user()->id == $model->id) {
            $dataIn = $request->all();
            try {
                $model = UserService::update($dataIn, $model);
            } catch (\Exception $e) {
                return redirect('admin/users/' . $id . '/edit')->withInput()->withErrors(trans("page.edit-error"));
            }
            if (Auth::user()->type == 1)
                return redirect('admin/users')->with('success', trans('all.success'));
            else
                return redirect('admin/users/' . $id . '/edit')->with('success', trans('all.success'));

        } else
            abort(403, 'Unauthorized Action');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy(Request $request, $id)
    {
        $model = UserService::delete($id);
        return redirect('admin/users')->with('success', trans('all.success'));
    }
}
