<?php

namespace App\Http\Controllers;

use App\Facades\LinkService;
use App\Facades\PageService;
use App\Mail\ContactUs;
use App\Mail\SendInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $data =  array();
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($locale,$id, $slug = '')
    {
        $result  = LinkService::getOne($id);
        $pageInfo['title'] = $result->name;
        $pageInfo['description'] = words( $result->description ,25, '.');

        return view('links.view' ,compact('result','pageInfo'));
    }

    public function contact()
    {
        $result = PageService::getOne(3);
        $pageInfo['title']  = $result->translation(App::getLocale())->name;
        $pageInfo['keywords'] = trans('site.home-keywords');
        $pageInfo['description'] = trans('site.description');
        return view('pages.contact' , compact('pageInfo'  , 'result'));
    }

    public function send_inquiry()
    {
        $result = PageService::getOne(2);
        $pageInfo['title']  = $result->translation(App::getLocale())->name;
        $pageInfo['keywords'] = trans('site.home-keywords');
        $pageInfo['description'] = trans('site.description');
        return view('pages.send_inquiry' , compact('pageInfo'  , 'result'));
    }

    public function sendMail(Request $request)
    {
        if ($request->ajax())
        {
            $this->data =  array('name' => $request->input('name') ,
                'email'=> $request->input('email') ,
                'phone'=> $request->input('phone') ,
                'msg' => $request->input('message'));
            $v = Validator::make(
                array('name' => $request->input('name') ,
                    'email'=> $request->input('email') ,
                    'phone'=> $request->input('phone') ,
                    'message' => $request->input('message'))
                ,array(
                'name' => 'required|max:255',
                'phone' => 'required|max:255',
                'email' => 'required|email',
                'message' => 'required'));
            if($v->fails()){
                $errors = $v->errors();
                $txt = "";
                if ( ! empty( $errors ) ) {
                    foreach ($errors->all() as $message){
                        $txt .= '<div class="text-danger"><p>' . $message . '</p></div>';
                    }

                }
                return response()->json([
                    'error' => true,
                    'result' => $txt,
                ]);
            }else{
                Mail::to(config("mail.master")["address"])->send(new ContactUs($this->data));
                Mail::to($this->data['email'])->send(new ContactUs($this->data));
                return response()->json([
                    'error' => false,
                    'result' => 1,
                ]);
            }
        }else{
            abort(403 , 'Unauthorized action');
        }
    }

    public function sendMailInquiry(Request $request)
    {
        if ($request->ajax())
        {
            $this->data =  array('name' => $request->input('name') ,
                'email'=> $request->input('email') ,
                'phone'=> $request->input('phone') ,
                'msg' => $request->input('message'));
            $v = Validator::make(
                array('name' => $request->input('name') ,
                    'email'=> $request->input('email') ,
                    'phone'=> $request->input('phone') ,
                    'message' => $request->input('message'))
                ,array(
                'name' => 'required|max:255',
                'phone' => 'required|max:255',
                'email' => 'required|email',
                'message' => 'required'));
            if($v->fails()){
                $errors = $v->errors();
                $txt = "";
                if ( ! empty( $errors ) ) {
                    foreach ($errors->all() as $message){
                        $txt .= '<div class="text-danger"><p>' . $message . '</p></div>';
                    }

                }
                return response()->json([
                    'error' => true,
                    'result' => $txt,
                ]);
            }else{
                Mail::to(config("mail.master")["address"])->send(new SendInquiry($this->data));
                Mail::to($this->data['email'])->send(new SendInquiry($this->data));
                return response()->json([
                    'error' => false,
                    'result' => 1,
                ]);
            }
        }else{
            abort(403 , 'Unauthorized action');
        }
    }

}
