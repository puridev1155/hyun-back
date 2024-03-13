<?php

namespace App\Http\Controllers\Admin\Statistics;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\Country;
use App\Models\Payment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PostStatsController extends Controller
{
    public function index(Request $request)
    {
        //전체 카테고리당 포스트 수 (이벤트, 오디션, 티켓)
        $totalPostByCategory = Post::groupBy('category_id')
                        ->select('category_id', DB::raw('COUNT(*) as post_count'))
                        ->get()
                        ->map(function ($item) {
                            return [
                                'category' => Category::find($item->category_id)->category_name,
                                'post_count' => $item->post_count,
                            ];
                        });

        //Vote 진행중
        $activePosts = Post::where('end_date', '>', Carbon::now())->count();

        //vote 종료
        $endedPosts = Post::where('end_date', '<=', Carbon::now())->count();

        //vote unlimited
        $unlimitedPosts = Post::whereNull('end_date')->count();

        //공개수 비공개수
        $publicPosts = Post::where('public', 1)->count();
        $privatePosts = Post::where('public', 0)->count();

        //Post당 몇명 결제하였는지 수
        $paymentByPost = Payment::where('payment_type', 'posts')
                                ->groupBy('pay_id')
                                ->select('pay_id', DB::raw('COUNT(*) as payment_count'))
                                ->get()
                                ->map(function ($item) {
                                    return [
                                        'post_id' => $item->pay_id??"No Post",
                                        'payment_count' => $item->payment_count,
                                    ];
                                });

        //전체 lang_id 포스트 수 (이벤트, 오디션, 티켓)
        $totalPostByLanguage = Post::groupBy('lang_id')
                        ->select('lang_id', DB::raw('COUNT(*) as post_count'))
                        ->get()
                        ->map(function ($item) {
                            return [
                                'category' => Country::find($item->lang_id)->name??"No country",
                                'post_count' => $item->post_count,
                            ];
                        });



        // Print the final JSON
        return response()->json(compact('totalPostByCategory', 'activePosts', 'endedPosts', 'unlimitedPosts'
                                , 'publicPosts', 'privatePosts', 'paymentByPost', 'totalPostByLanguage'));
    }
}
