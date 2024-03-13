<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\Admin\CategoryCollection;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if (Cache::has('categories')) {

            $categories = Cache::get('categories');

        } else {
            // filter (in url): ?filter[key]=value&filter[key]=value...
            // sort (in url): ?sort=key, key... (decending if -key)
            $categories = QueryBuilder::for(Category::class)
            ->allowedFilters([
                'catecode',
                'category_type',
                'category_name',
                'category_parent',
                ])
            ->defaultSort('catecode')
            ->allowedSorts(['category_type', 'category_name', 'category_parent'])
            ->paginate();

            Cache::put('categories', $categories, 20);
        }

        return new CategoryCollection($categories);
    }

    public function show(Request $request, Category $category)
    {
        return new CategoryResource($category);
    }

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = Category::create($validated);

        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();
        $category->update($validated);

        return new CategoryResource($category);
    }

    public function destroy(Request $request, Category $category)
    {
        $category->delete();

        return response()->noContent();
    }
}
