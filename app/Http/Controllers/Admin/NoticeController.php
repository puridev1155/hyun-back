<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notice;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\NoticeResource;
use App\Http\Resources\Admin\NoticeCollection;
use App\Http\Requests\Admin\StoreNoticeRequest;
use App\Http\Requests\Admin\UpdateNoticeRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NoticeController extends Controller
{

    public function index(Request $request)
    {
        // filter (in url): ?filter[key]=value&filter[key]=value...
        // sort (in url): ?sort=key, key... (decending if -key)
        $notices = QueryBuilder::for(Notice::class)
        ->allowedFilters([
            'category_id',
            'title',
            'info',
            'user_type',
            ])
        ->defaultSort('category_id')
        ->allowedSorts(['title', 'public', 'user_type'])
        ->paginate();

        return new NoticeCollection($notices);
    }


    public function show(Request $request, Notice $notice)
    {
        return new NoticeResource($notice);
    }


    public function store(StoreNoticeRequest $request)
    {
        // Find the category by its ID, or throw an exception if not found
        try {
            $category = Category::findOrFail($request->category_id);
        } catch (ModelNotFoundException $e) {
            // Category with the given ID was not found
            return response()->json(['error' => 'Category not found'], 404);
        }

        $validated = $request->validated();
        $notice = Notice::create($validated);

        return new NoticeResource($notice);
    }


    public function update(UpdateNoticeRequest $request, Notice $notice)
    {
        // Find the category by its ID, or throw an exception if not found
        if($request->has('category_id')) {
            try {
                $category = Category::findOrFail($request->category_id);
            } catch (ModelNotFoundException $e) {
                // Category with the given ID was not found
                return response()->json(['error' => 'Category not found'], 404);
            }
        }

        $validated = $request->validated();
        $notice->update($validated);

        return new NoticeResource($notice);
    }


    public function destroy(Request $request, Notice $notice)
    {
        $notice->delete();

        return response()->noContent();
    }
}
