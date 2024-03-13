<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\NoticeResource;
use App\Http\Resources\Admin\NoticeCollection;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        // filter (in url): ?filter[key]=value&filter[key]=value...
        // sort (in url): ?sort=key, key... (decending if -key)
        $notices = QueryBuilder::for(Notice::class)
        ->selectRaw('notices.id, categories.category_name, notices.title, notices.updated_at')
        ->leftJoin('categories', 'notices.category_id', '=', 'categories.id')
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
}
