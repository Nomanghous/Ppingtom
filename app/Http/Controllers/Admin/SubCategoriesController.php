<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySubCategoryRequest;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use App\Http\Requests\GetSubCategoriesById;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubCategoriesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('sub_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subCategories = ProductSubCategory::with(['category'])->get();

        return view('admin.subCategories.index', compact('subCategories'));
    }

    public function create()
    {
        abort_if(Gate::denies('sub_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = ProductCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.subCategories.create', compact('categories'));
    }

    public function store(StoreSubCategoryRequest $request)
    {
        $subCategory = ProductSubCategory::create($request->all());

        return redirect()->route('admin.sub-categories.index');
    }

    public function edit(SubCategory $subCategory)
    {
        abort_if(Gate::denies('sub_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = ProductCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $subCategory->load('category');

        return view('admin.subCategories.edit', compact('categories', 'subCategory'));
    }

    public function update(UpdateSubCategoryRequest $request, SubCategory $subCategory)
    {
        $subCategory->update($request->all());

        return redirect()->route('admin.sub-categories.index');
    }

    public function show(SubCategory $subCategory)
    {
        abort_if(Gate::denies('sub_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subCategory->load('category');

        return view('admin.subCategories.show', compact('subCategory'));
    }

    
    public function getById($id)
    {
        abort_if(Gate::denies('sub_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $subcategories = ProductSubCategory::where('category_id', $id)->pluck('name', 'id');
        return $subcategories;
    }

    


    public function destroy(SubCategory $subCategory)
    {
        abort_if(Gate::denies('sub_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroySubCategoryRequest $request)
    {
        ProductSubCategory::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
