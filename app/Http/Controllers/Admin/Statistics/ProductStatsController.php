<?php

namespace App\Http\Controllers\Admin\Statistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Country;

class ProductStatsController extends Controller
{
    //TODO PRODUCT STAT 추가
    public function index(Request $request) {
        //전체 카테고리당 PRODUCT 수
        $totalProductByCategory = Product::groupBy('category_id')
                        ->select('category_id', DB::raw('COUNT(*) as product_count'))
                        ->get()
                        ->map(function ($item) {
                            return [
                                'category_id' => $item->category_id,
                                'category' => Category::find($item->category_id)->category_name,
                                'product_count' => $item->product_count,
                            ];
                        });

        $totalProductByCountry = Product::groupBy('lang_id')
        ->select('lang_id', DB::raw('COUNT(*) as lang_count'))
        ->get()
        ->map(function ($item) {
            return [
                'lang_id' =>$item->lang_id,
                'country_name' => Country::find($item->lang_id)->name,
                'country_count' => $item->lang_count,
            ];
        });

        $totalProduct = Product::count();

        //재고 
        $sell = Product::where('in_stock', '>', 0)->count();

        //완판
        $sold = Product::where('in_stock', '=', 0)->count();


        // Print the final JSON
        return response()->json(compact('totalProduct', 'totalProductByCategory', 'totalProductByCountry', 'sell', 'sold'));
    }
}

