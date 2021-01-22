<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreMainCategoryRequest;
use App\Http\Requests\UpdateMainCategoryRequest;
use App\Http\Resources\Admin\MainCategoryResource;
use App\Models\MainCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MainCategoriesApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('main_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MainCategoryResource(MainCategory::all());
    }

    public function store(StoreMainCategoryRequest $request)
    {
        $mainCategory = MainCategory::create($request->all());

        if ($request->input('photo', false)) {
            $mainCategory->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }

        return (new MainCategoryResource($mainCategory))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(MainCategory $mainCategory)
    {
        abort_if(Gate::denies('main_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MainCategoryResource($mainCategory);
    }

    public function update(UpdateMainCategoryRequest $request, MainCategory $mainCategory)
    {
        $mainCategory->update($request->all());

        if ($request->input('photo', false)) {
            if (!$mainCategory->photo || $request->input('photo') !== $mainCategory->photo->file_name) {
                if ($mainCategory->photo) {
                    $mainCategory->photo->delete();
                }

                $mainCategory->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
            }
        } elseif ($mainCategory->photo) {
            $mainCategory->photo->delete();
        }

        return (new MainCategoryResource($mainCategory))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(MainCategory $mainCategory)
    {
        abort_if(Gate::denies('main_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mainCategory->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
