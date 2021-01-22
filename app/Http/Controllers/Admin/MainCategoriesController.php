<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyMainCategoryRequest;
use App\Http\Requests\StoreMainCategoryRequest;
use App\Http\Requests\UpdateMainCategoryRequest;
use App\Models\MainCategory;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class MainCategoriesController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('main_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mainCategories = MainCategory::with(['media'])->get();

        return view('admin.mainCategories.index', compact('mainCategories'));
    }

    public function create()
    {
        abort_if(Gate::denies('main_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.mainCategories.create');
    }

    public function store(StoreMainCategoryRequest $request)
    {
        $mainCategory = MainCategory::create($request->all());

        if ($request->input('photo', false)) {
            $mainCategory->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $mainCategory->id]);
        }

        return redirect()->route('admin.main-categories.index');
    }

    public function edit(MainCategory $mainCategory)
    {
        abort_if(Gate::denies('main_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.mainCategories.edit', compact('mainCategory'));
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

        return redirect()->route('admin.main-categories.index');
    }

    public function show(MainCategory $mainCategory)
    {
        abort_if(Gate::denies('main_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.mainCategories.show', compact('mainCategory'));
    }

    public function destroy(MainCategory $mainCategory)
    {
        abort_if(Gate::denies('main_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mainCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyMainCategoryRequest $request)
    {
        MainCategory::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('main_category_create') && Gate::denies('main_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new MainCategory();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
