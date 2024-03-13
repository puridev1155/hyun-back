<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\BannerResource;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->lang_id ? $request->lang_id : 1;
        // 홈
        //if (Cache::has('home')) {

            //$home = Cache::get('home');

        //} else {

            // 상단 - 광고 베너 상단 (최신순으로 3개 불러오기)
            $top_banner = Banner::selectRaw('banners.id, banners.banner_title, banners.url_link')->where('lang_id', 1)->where('location', 'top')->latest()->limit(3)->get();
            $top_banner = BannerResource::collection($top_banner);



            // 하단(인기몰이 광고 - 광고 베너 - 최신순으로 6개 불러오기)
            $bottom_banner = Banner::selectRaw('banners.id, banners.banner_title, banners.url_link')->where('lang_id', 1)->where('location', 'bottom')->latest()->limit(6)->get();
            $bottom_banner = BannerResource::collection($bottom_banner);

            // "인기 아이템 (Product Api  인기 6개 불러오기)
            // (카테고리명, 제품명, 가격)"
            $popular_product = Product::selectRaw('products.id, products.brand, products.product_name, products.memo_price')->where('products.lang_id', $lang)
                //->with(['pprices' => function ($query) {
                //                            $query->orderBy('created_at', 'desc');
               //                         }])
                //                        ->where('products.lang_id', 1)
                                        ->orderBy('products.id', 'desc')
                                        ->take(6)
                                        ->get();

            $popular_product = ProductResource::collection($popular_product);

            $home = compact(['top_banner', 'bottom_banner', 'popular_product']);

            //Cache::put('home', $home, 3);
        //}

        //Log::debug(['home202' => $home]);
        return response()->json($home);
    }

    public function ads(Request $request) {
        $ad_banner = Banner::selectRaw('banners.id, banners.banner_title, banners.url_link')->where('location', 'ad_banner')->latest()->limit(2)->get();
        $ad_banner = BannerResource::collection($ad_banner);
        return response()->json(compact('ad_banner'));
    }
}
