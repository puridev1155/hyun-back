<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\Eprice; //추가됨
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Resources\Admin\PostCollection;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->itemsPerPage ? $request->itemsPerPage : 10;

            $posts = QueryBuilder::for(Post::class)
            ->selectRaw('id, category_id, title, created_at, updated_at')
            ->allowedFilters([
                AllowedFilter::callback('search', function($query, $value){
                    $query->where('title', 'LIKE', '%' . $value . '%');
                }),
                AllowedFilter::callback('startdate', function ($query, $value) {
                    return $query->where('updated_at', '>=', $value);
                }),
                AllowedFilter::callback('enddate', function ($query, $value) {
                    return $query->where('updated_at', '<=', Carbon::parse($value)->addDay());
                }),
                'title',
                'category_id',
                ])
            ->defaultSort('-created_at')
            ->allowedSorts(['category_id'])
            ->paginate($itemsPerPage);
        return PostResource::collection($posts);

    }

    public function show(Request $request, Post $post)
    {
        return new PostResource($post);
    }


    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::user()->id;

        $post = Auth::user()->posts()->create($validated);
        //insert image
        if($request->file('image')) {
        $post->addMediaFromRequest('image')
            ->toMediaCollection('post_image', 's3');//(collection name column, filesystems.php::disk)
        }
        return new PostResource($post);
    }

     public function storeImage(Request $request)
     {
         //내용에 이미지가 첨부되어야 할 경우 아래 실행
         if($request->hasFile('image')) {
         $media = Auth::user()->addMedia($request->file('image'))
         ->toMediaCollection('post_image', 's3');
             return response()->json(['success' => true, 'data' => $media]);
         }

     }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $validated = $request->validated();

        $post->update($validated); 
        //TODO array로 받아온 데이터를 어떻게 처리할지 고민

        $prices = json_decode($request->eprices, true); //json 파일을 array로 변환


        if($prices) {
            foreach($prices as $index => $item){
                //$item = 3000; 가격
                Eprice::where('id', $item['id'])->update([
                    'title' => $item['title'],
                    'price' => (int)$item['price'],
                ]);
                
            }
        }

        if(!$request->file('image') && !$request->original_url) { //삭제한 경우
            $post->clearMediaCollection('post_image');
        } else if ($request->file('image')) { //새로운 이미지와 이전 이미지 컨디션
            if($request->original_url) {
                $post->clearMediaCollection('post_image');
                $post->addMediaFromRequest('image')
            ->toMediaCollection('post_image', 's3');//(collection_name column, filesystems.php::disk)
            } else {
                $post->addMediaFromRequest('image')
            ->toMediaCollection('post_image', 's3');//(collection_name column, filesystems.php::disk)
            }
        }

        
        return new PostResource($post);
    }

    public function destroy(Request $request, Post $post)
    {
        //$post->delete();
        $post->forceDelete();

        return response()->noContent();
    }

    public function showTrash(Request $request)
    {
        $posts = Post::onlyTrashed()->paginate();

        return new PostCollection($posts);
    }

    public function restoreTrash(Request $request, int $id)
    {
        $post = Post::withTrashed()->find($id);

        $post->restore();

        return new PostResource($post);
    }

    public function destroyTrash(Request $request, int $id)
    {
        $post = Post::withTrashed()->find($id);

        $post->forceDelete();

        return response()->noContent();
    }

    
}
