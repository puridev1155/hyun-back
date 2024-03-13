<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Banner;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\BannerResource;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->itemsPerPage ? $request->itemsPerPage : 10;

        if (Cache::has('banners')) {
            $banners = Cache::get('banners');
        } else {
            // filter (in url): ?filter[key]=value&filter[key]=value...
            // sort (in url): ?sort=key, key... (decending if -key)
            $banners = QueryBuilder::for(Banner::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function($query, $value){
                    $query->where('banner_title', 'LIKE', '%' . $value . '%');
                }),
                AllowedFilter::callback('startdate', function ($query, $value) {
                    return $query->where('updated_at', '>=', $value);
                }),
                AllowedFilter::callback('enddate', function ($query, $value) {
                    return $query->where('updated_at', '<=', Carbon::parse($value)->addDay());
                }),
                'banner_title',
                'category_id'
                ])
            ->defaultSort('-created_at')
            ->allowedSorts(['category_id', 'country_id'])
            ->paginate($itemsPerPage);

            //Cache::put('banners', $banners, 1);
        }

        // return new BannerCollection($banners);
        return BannerResource::collection($banners);

    }

    public function show(Request $request, Banner $banner)
    {
        return new BannerResource($banner);
    }


    public function store(StoreBannerRequest $request)
    {

        // Find the category by its ID, or throw an exception if not found
        try {
            $category = Category::findOrFail($request->category_id);
        } catch (ModelNotFoundException $e) {
            // Category with the given ID was not found
            return response()->json(['error' => 'Category not found'], 404);
        }

        $validated = $request->validated();
        $banner = $category->banners()->create($validated);

        //insert image
        if($request->hasFile('image')) {
        $banner->addMediaFromRequest('image')
            ->toMediaCollection('banner_image', 's3');//(collection name column, filesystems.php::disk)
        }

        return new BannerResource($banner);
    }

    //  public function storeImage(Request $request)
    //  {
    //      //내용에 이미지가 첨부되어야 할 경우 아래 실행
    //      if($request->hasFile('image')) {
    //      $media = Auth::user()->addMedia($request->file('image'))
    //      ->toMediaCollection('banner_image', 's3');
    //          return response()->json(['success' => true, 'data' => $media]);
    //      }

    //  }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {

        $validated = $request->validated();
        $banner->update($validated);

        if ($request->hasFile('image')) { //새로운 이미지와 이전 이미지 컨디션

            if($banner->hasMedia('banner_image')) {

                $banner->clearMediaCollection('banner_image');
                $banner->addMediaFromRequest('image')
                ->toMediaCollection('banner_image', 's3');//(collection_name column, filesystems.php::disk)
                return response()->json('there image');

            } else {

                $banner->addMediaFromRequest('image')
                ->toMediaCollection('banner_image', 's3');//(collection_name column, filesystems.php::disk)
                return response()->json('no image');

            }

        }


        return new BannerResource($banner);
    }

    public function destroy(Request $request, Banner $banner)
    {
        $banner->delete();

        return response()->noContent();
    }
}
