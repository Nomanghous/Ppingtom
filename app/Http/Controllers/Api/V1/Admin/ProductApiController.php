<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\GetNearbyNewsRequest;
use App\Http\Requests\GetProductByTopic;
use App\Http\Requests\UpvoteProductRequest;
use App\Http\Requests\GetNearbyNewsWithDateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductTag;
use App\Models\Location;
use App\Models\Vote;
use App\Models\VoteTypes;
use App\Models\ProductLocation;
use Gate;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductApiController extends Controller
{
    use MediaUploadingTrait;
    public function index()
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return new ProductResource(Product::with(['categories', 'tags'])->get());
    }
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->all());
        $product->categories()->sync($request->input('categories', []));
        $product->tags()->sync($request->input('tags', []));
        if ($request->input('photo', false)) {
            $product->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }
        if ($request->input('media_asset', false)) {
            $product->addMedia(storage_path('tmp/uploads/' . $request->input('media_asset')))->toMediaCollection('media_asset');
        }
        return (new ProductResource($product))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
    public function show(Product $product)
    {
        abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return new ProductResource($product->load(['categories', 'tags']));
    }
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->all());
        $product->categories()->sync($request->input('categories', []));
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
        return (new ProductResource($product))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }
    public function destroy(Product $product)
    {
        abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $product->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
    public function fetchNearbyProducts(GetNearbyNewsRequest $request)
    {
        $distance = env('NEARBY_NEWS_DISTANCE');
        $query = Location::select(DB::raw('*, ( 6367 * acos( cos( radians(' . $request['latitude'] . ') ) * cos( radians( latitude ) ) * cos( radians( logitude ) - radians(' . $request['logitude'] . ') ) + sin( radians(' . $request['latitude'] . ') ) * sin( radians( latitude ) ) ) ) AS distance'))
            ->having('distance', '<', $distance)
            ->orderBy('distance')->pluck('id');
        $result = $this->getProductQuery($query, null);
        return response()->json(
            [
                'status_code' => 200,
                'message' => 'success',
                'data' => [
                    'products' => $result,
                ]
            ]
        );        
    }

    public function fetchNearbyProductsWithDate(GetNearbyNewsWithDateRequest $request)
    {
        $distance = env('NEARBY_NEWS_DISTANCE');
        $query = Location::select(DB::raw('*, ( 6367 * acos( cos( radians(' . $request['latitude'] . ') ) * cos( radians( latitude ) ) * cos( radians( logitude ) - radians(' . $request['logitude'] . ') ) + sin( radians(' . $request['latitude'] . ') ) * sin( radians( latitude ) ) ) ) AS distance'))
            ->having('distance', '<', $distance)
            ->orderBy('distance')->pluck('id');
        $result = $this->getProductQuery($query, $request->date);
        return response()->json(
            [
                'status_code' => 200,
                'message' => 'success',
                'data' => [
                    'products' => $result,
                ]
            ]
        );        
    }


    private function getProductQuery($locationIds, $withDate){
        if($withDate == null){
           return Product::with('category','subcategories','user:id,name', 'location:id,address,city,country,latitude,logitude')->withCount('upVotes','bookmarks', 'userLiked','userBookmarked')->whereIn('location_id', $locationIds)->get();
        }else{
            return Product::with('category','subcategories','user:id,name', 'location:id,address,city,country,latitude,logitude')->withCount('upVotes','bookmarks', 'userLiked','userBookmarked')->whereIn('location_id', $locationIds)->whereDate('created_at',date($withDate))->get();
        }
        
    }

    
    public function fetchProductById(GetNearbyNewsRequest $request)
    {
        $result = Product::whereIn('location_id', $query)->get();
        return response()->json(
            [
                'status_code' => 200,
                'message' => 'success',
                'data' => [
                    'products' => $result,
                ]
            ]
        );
        
        
    }
    public function fetchProductByTopic(GetProductByTopic $request)
    {
        abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $query = ProductTag::where('name','like','%'.$request->topic."%")->get("id");
        $result = response();
        if($query->isEmpty()){
            $query = Location::where('address','like','%'.$request->topic."%")->get("id");
            $result = $this->getProductQuery($query, null);
        }else{
            $result = Product::with('category','subcategories','user:id,name', 'location:id,address,city,country,latitude,logitude')->withCount('upVotes','bookmarks','userLiked','userBookmarked')->whereHas('tags', function($q) use($query) {
                $q->whereIn('id', $query);
            })->get();
        }

        return response()->json(
            [
                'status_code' => 200,
                'message' => 'success',
                'data' => [
                    'products' => $result,
                ]
            ]
        );  
    }
    
    public function vote(Request $request)
    {
        $result = Product::withCount('upVotes','bookmarks')->get();
        return response()->json(
            [
                'status_code' => 200,
                'message' => 'success',
                'data' => $result
            ]
        );
    }

    public function upvoteProduct(UpvoteProductRequest $request)
    {
        $alreadyVoted = Vote::where([['user_id',$request->user_id], ['product_id', $request->product_id],['type', $request->type]])->first();
        if($alreadyVoted){
            Vote::where([['user_id',$request->user_id], ['product_id', $request->product_id],['type', $request->type]])->delete();
        }else{
            $vote = Vote::create($request->all());
        }
        
        return response()->json(
            [
                'status_code' => 200,
                'message' => 'success',
                'data' => []
            ]
        );
    }
}
