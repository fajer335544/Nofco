<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class LinkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required',
            'image' => 'image',
            'file.*'=> 'mimes:pdf,jpeg,bmp,png,doc,docx',
        ];
    }
//
//    public static function getMessages()
//    {
//        $messages = array();
//        $messages['name.required'] = trans('user.name-required');
//        $messages['name.max:255'] = trans('user.name-max');
//        $messages['email.required'] = trans('user.email-required');
//        $messages['email.email'] = trans('user.name-email');
//        $messages['email.max:255'] = trans('user.name-max');
//        $messages['email.unique'] = trans('user.name-unique');
//
//        return $messages;
//    }
//
//    public  function messages()
//    {
//        return $this->getMessages();
//    }
    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }
}