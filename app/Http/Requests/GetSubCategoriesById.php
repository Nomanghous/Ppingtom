<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Gate;

class GetSubCategoriesById extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('sub_category_show');
    }

    public function rules()
    {
        return [];
        // return [
        //     'id' => [
        //         'required',
        //         'integer',
        //     ],
        // ];
    }
}
