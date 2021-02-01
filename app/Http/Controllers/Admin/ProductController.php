<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Location;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductTag;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::with(['subcategories', 'tags', 'user', 'location', 'media'])->get();
        $categories = ProductCategory::all()->pluck('name', 'id');


        return view('admin.products.index', compact('products','categories'));
    }

    public function create()
    {
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = ProductCategory::all()->pluck('name', 'id');
        
        $tags = ProductTag::all()->pluck('name', 'id');

        $users = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $locations = Location::all()->pluck('address', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.products.create', compact('categories', 'tags', 'users', 'locations'));
        
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->all());
        $product->tags()->sync($request->input('tags', []));
        $product->subcategories()->sync($request->input('subcategories', []));
        if ($request->input('photo', false)) {
            $product->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }

        if ($request->input('media_asset', false)) {
            $product->addMedia(storage_path('tmp/uploads/' . $request->input('media_asset')))->toMediaCollection('media_asset');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $product->id]);
        }

        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product)
    {
        abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = ProductCategory::all()->pluck('name', 'id');
        $subcategories = ProductSubCategory::all()->pluck('name', 'id');
        $tags = ProductTag::all()->pluck('name', 'id');

        $users = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $locations = Location::all()->pluck('address', 'id')->prepend(trans('global.pleaseSelect'), '');

        $product->load('subcategories', 'tags', 'user', 'location');

        
        return view('admin.products.edit', compact('categories','subcategories', 'tags', 'users', 'locations', 'product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->all());
        $product->subcategories()->sync($request->input('subcategories', []));
        $product->tags()->sync($request->input('tags', []));

        if ($request->input('photo', false)) {
            if (!$product->photo || $request->input('photo') !== $product->photo->file_name) {
                if ($product->photo) {
                    $product->photo->delete();
                }

                $product->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
            }
        } elseif ($product->photo) {
            $product->photo->delete();
        }

        if ($request->input('media_asset', false)) {
            if (!$product->media_asset || $request->input('media_asset') !== $product->media_asset->file_name) {
                if ($product->media_asset) {
                    $product->media_asset->delete();
                }

                $product->addMedia(storage_path('tmp/uploads/' . $request->input('media_asset')))->toMediaCollection('media_asset');
            }
        } elseif ($product->media_asset) {
            $product->media_asset->delete();
        }

        return redirect()->route('admin.products.index');
    }

    public function show(Product $product)
    {
        abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->load('subcategories', 'tags', 'user', 'location');

        return view('admin.products.show', compact('product'));
    }

    public function destroy(Product $product)
    {
        abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductRequest $request)
    {
        Product::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('product_create') && Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Product();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}