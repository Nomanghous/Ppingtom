<?php

namespace App\Http\Requests;

use App\Models\MainCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateMainCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('main_category_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
