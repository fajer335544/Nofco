<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BlocksRequest extends FormRequest
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
        $rules  = [
            'name' => 'required|max:255',
            'post_date' => 'nullable|date',
            'image' => 'image',
            'file.*' => 'mimes:jpeg,bmp,png,pdf,docx,doc,xls,xlsx',
        ];
        if(is_array(config('app.locales')))
        {
            foreach (config('app.locales') as $key => $value) {
                $rules[$key.'.name'] = 'required|max:255';
            }
        }
        return $rules;
    }

    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }
}
