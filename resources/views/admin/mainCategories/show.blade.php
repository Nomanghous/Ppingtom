@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.mainCategory.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.main-categories.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.mainCategory.fields.id') }}
                        </th>
                        <td>
                            {{ $mainCategory->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.mainCategory.fields.name') }}
                        </th>
                        <td>
                            {{ $mainCategory->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.mainCategory.fields.description') }}
                        </th>
                        <td>
                            {{ $mainCategory->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.mainCategory.fields.photo') }}
                        </th>
                        <td>
                            @if($mainCategory->photo)
                            <a href="{{ $mainCategory->photo->getUrl() }}" target="_blank" style="display: inline-block">
                                <img src="{{ $mainCategory->photo->getUrl('thumb') }}">
                            </a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.main-categories.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection