<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ArticleFileRequest
 * @package App\Http\Requests
 */
class FileRequest  extends FormRequest
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
            'name' => 'required|min:4',
            'src.*' => 'mimes:jpeg,bmp,png,pdf,docx,doc,xls,xlsx'
        ];
    }
    public static function getMessages()
    {
        $messages = array();
        $messages['title.required'] = trans('file.request_title_required');
        $messages['title.min'] = trans('file.request_title_min');
        $messages['image.required'] = trans('file.request_image_required');
        $messages['image.image'] = trans('file.request_must_be_image');

        return $messages;
    }

    public  function messages()
    {
        return $this->getMessages();
    }
    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }
}
