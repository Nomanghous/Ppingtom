<?php

namespace App\Http\Requests;

use App\Models\Location;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateLocationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('location_edit');
    }

    public function rules()
    {
        return [
            'city'     => [
                'string',
                'required',
            ],
            'address'  => [
                'string',
                'required',
            ],
            'country'  => [
                'string',
                'required',
            ],
            'zip_code' => [
                'string',
                'nullable',
            ],
            'latitude' => [
                'numeric',
                'required',
            ],
            'logitude' => [
                'numeric',
                'required',
            ],
        ];
    }
}