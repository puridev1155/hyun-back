<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\Product;
use App\Models\Account;
use GuzzleHttp\Exception\ClientException;
use App\Models\User;
use App\Models\Qrcodeinfo;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Resources\AuditionResource;
use App\Http\Resources\PostPageResource;
use App\Http\Requests\PostRequest;
use App\Http\Requests\GetPostRequest;
use Illuminate\Support\Facades\Auth;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{



    public function index(Request $request)
    {
    // 검색하기 [ 필터 추가 ]
    // 전체 티켓 - 최신 10개, 날짜에 맞춰서 진행중, 종료 분별해서  보내주기
    // (title, cost, memo, banner)
    $itemsPerPage = $request->itemsPerPage ? $request->itemsPerPage : 10;


    //페이지네이션 가능한 POST
    if (Cache::has('posts')) {
        $posts = Cache::get('posts');
    } else {
        // filter (in url): ?filter[key]=value&filter[key]=value...
        // sort (in url): ?sort=key, key... (decending if -key)
        $posts = QueryBuilder::for(Post::class)
        ->paginate($itemsPerPage);
        $posts = PostPageResource::collection($posts);
        Cache::put('posts', $posts, 3);
        }
        
        //Log::debug(['post' => $posts, 'date' => Carbon::now()]);

    return $posts;

    }

    public function topCarousel(Request $request) {

        $lang = $request->lang_id ? $request->lang_id : 1;

            $events = Post::selectRaw('posts.id, posts.lang_id, posts.title, posts.title, posts.category_id, posts.memo_price, posts.memo_info');
            if($request->category) {
                $events = $events->where('category_id', $request->category);
            }
            $events = $events->where('board', 'event')->where('lang_id', $lang)->latest()->limit(7)->get();
            $events = PostPageResource::collection($events);
            return response()->json($events);
    } 


    // 티켓 상세 페이지 (App9)
    // (티케 썸네일 이밎, title, ticket_type, available_ticket, cost, content)
    // (베너 하단)
    public function show(Request $request, Post $audition)
    {
        try {
            Log::debug(['message' => 'show 단계' ]);
            Log::debug(['post' => $audition, 'date' => Carbon::now()]);
            return new AuditionResource($audition);

        } catch (ClientException $e) {
            // You can log the error for debugging purposes
            //TODO 이것이 왜 안되는지 확인 필요 
            return response()->json(['success' => false, 'message' => 'Failed', 'token' => null]);
        }

    }

    public function store(PostRequest $request) {

        try {

            //Log::debug(['post_store' => $request, 'date' => Carbon::now()]);
            $validated = $request->validated();
            $validated['price'] = $validated['price'] ? $validated['price'] : 0;
            $validated['amount'] = $validated['amount'] ? $validated['amount'] : 1;
            $post = Post::selectRaw("id, title, memo_info, post_type, category_id")->where('id', $validated['pay_id'])->first() ?? throw new \Exception("Title cannot be null");
            $user = User::findOrFail(Auth::user()->id);
            $validated['user_id'] = $user->id;
            $validated['payment_type'] = 'posts';//고정 값
            $validated['title'] = $post->title;
            $payment = Payment::create($validated);
            $payment['original_url'] = $post->getFirstMedia('post_image')->original_url?? 'https://d2yu2kebcy91bk.cloudfront.net/220/default.jpg';
            $payment['description'] = $post->memo_info;
            $payment['post_type'] = $post->post_type;
            $payment['name'] = $user->name;
            $payment['currency'] = '₩'; //currency 종류별로 

            $data = [
            //audition
            1 => [
                //free
                'free' => ['social_type' => ['tiktok', 'instagram'], 'social_name' => true, 'video_url' => true, 
                'video_url_placeholder' => 'https://www...', 'bank_account' => false, 
                'bank_username' => false, 'social_number' => false, 'participant' => false, 'participant_count' => false, 'phone' => false,
                'paypal_id' => false, 'payment_btn' => 'free'], 

                //pay
                'pay' => ['social_type' => ['tiktok', 'instagram'], 'social_name' => true, 'video_url' => true, 
                'video_url_placeholder' => 'https://www...', 'bank_account' => false, 
                'bank_username' => false, 'social_number' => false, 'participant' => false, 'participant_count' => false, 'phone' => false,
                'paypal_id' => false,  'payment_btn' => $user->lang_id == 1 ? 'toss' : 'paypal'],
                
                //challenge
                'challenge' => ['social_type' => ['tiktok'], 'social_name' => true, 'video_url' => true, 
                'video_url_placeholder' => 'https://www.tiktok.com...', 'bank_account' => $user->lang_id == 1 ? true : false, 
                'bank_username' => $user->lang_id == 1 ? true : false, 'social_number' => $user->lang_id == 1 ? true : false,
                'participant' => false, 'participant_count' => false, 'phone' => true,
                'paypal_id' => $user->lang_id != 1 ? true : false, 'payment_btn' => 'free']
            ],
            //ticket
            2 => [
                //학원 수강권 티켓
                'education' => ['social_type' => ['instagram'], 'social_name' => true, 'video_url' => false, 
                'video_url_placeholder' => 'https://www...', 'bank_account' => false, 
                'bank_username' => false, 'social_number' => false, 'participant' => false, 'participant_count' => false, 'phone' => true,
                'paypal_id' => false, 'payment_btn' => 
                ($user->lang_id == 1 && $post->memo_price) ? "toss" : ($post->memo_price ? "paypal" : "free"),], 

                //대회참가 티켓
                'contest' => ['social_type' => ['instagram'], 'social_name' => true, 'video_url' => false, 
                'video_url_placeholder' => 'https://www...', 'bank_account' => false, 
                'bank_username' => false, 'social_number' => false, 'participant' => true, 'participant_count' => true, 'phone' => true,
                'paypal_id' => false, 'payment_btn' => 
                ($user->lang_id == 1 && $post->memo_price) ? "toss" : ($post->memo_price ? "paypal" : "free"),], 

                //대회관람 티켓
                'watch' => ['social_type' => ['instagram'], 'social_name' => true, 'video_url' => false, 
                'video_url_placeholder' => 'https://www...', 'bank_account' => false, 
                'bank_username' => false, 'social_number' => false, 'participant' => false, 'participant_count' => false, 'phone' => true,
                'paypal_id' => false, 'payment_btn' => 
                ($user->lang_id == 1 && $post->memo_price) ? "toss" : ($post->memo_price ? "paypal" : "free"),], 

                //순위투표 티켓
                'vote' => ['social_type' => ['instagram'], 'social_name' => true, 'video_url' => false, 
                'video_url_placeholder' => 'https://www...', 'bank_account' => false, 
                'bank_username' => false, 'social_number' => false, 'participant' => false, 'participant_count' => false, 'phone' => true,
                'paypal_id' => false, 'payment_btn' => 
                ($user->lang_id == 1 && $post->memo_price) ? "toss" : ($post->memo_price ? "paypal" : "free"),], 

            ]
        ];

        $payment['permit'] = $data[$post->category_id ? $post->category_id : 0][$post->post_type];
        Log::debug(['post_store' => $payment, 'date' => Carbon::now()]);
        return response()->json($payment);

        } catch (ClientException $e) {
                // You can log the error for debugging purposes
                //TODO 이것이 왜 안되는지 확인 필요 
                return response()->json(['success' => false, 'message' => 'Failed', 'token' => null]);
        }
        
    }

    public function update(PostRequest $request) {
        $validated = $request->validated();

        //Challenge
        $account = [];
        $account['bank_account'] = $request->bank_account ?  $request->bank_account : null;
        $account['bank_username'] = $request->bank_username ? $request->bank_username : null;
        $account['social_number'] = $request->social_number ? $request->social_number : null;
        $account['paypal_id'] =  $request->paypal_id ? $request->paypal_id : null;
        $account['payment_id'] = $validated['id'];
        $account['user_id'] = Auth::user()->id;
        
        $accountData = Account::insert(
            $account );


        //소셜 ID 필요시 적용
        if($request->social_type) {
            $validated[$request->social_type] = $request->social_type;
        }
        //TODO 내용 업데이트
        $validated['status'] = "hold";

        //get PostType
        //$payment = Payment::selectRaw('posts.post_type')->leftJoin('posts', 'payments.pay_id', '=', 'posts.id')->where('payments.id', $validated['id'])->first();
        $payment = Payment::where('id', $validated['id'])->update($validated);
        $payment = Payment::where('id', $validated['id'])->first();
        

        $user = User::findOrFail(Auth::user()->id);

        $paypal = [
            'client_id' =>  env('PAYPAL_SANDBOX_CLIENT_ID'),
            'secret_id' =>  env('PAYPAL_SANDBOX_CLIENT_SECRET'),
            'total' => strval($payment['price'] * (int)$payment['amount']),
            'currency' => 'USD',
            'subtotal' => strval($payment['price'] * (int)$payment['amount']),
            'description' => '백에드에서 상품 내용 들어가게 설정해놓겠습니다',
            'items' => [
                [ 
                    'name' => $payment['id'] . '_'.$payment['title'],
                    'quantity' => (int)$payment['amount'], // Provide a quantity
                    'price' => strval($payment['price']), // Provide a price
                    'currency' => 'USD',
                ]
            ],
            'shipping_address' => [
                'recipient_name' => $user->id . '_' . $user->name,
                'line1' => null,
                'phone' => $user->phone ? $user->phone : null
            ],
            "return_url" => route("payment_success", ['id' => $payment['id']]),
            "cancel_url" => route("payment_cancel"),
            
        ];

        $toss = [
            'success' => true,
            'clientKey' =>  env('TOSS_CLIENT_ID'),
            'data' => [
                'paymentMethod'=> '카드',
                'orderId' => 'tosspayments-' . $payment['id'],
                "orderName" => $payment['title'],
                'amount' => $payment['price'] * (int)$payment['amount'],
                'customerName' => '이름',
                'customerEmail' => null,       
            ],
            "success" => route("payment_success", ['id' => $payment['id']]),
            "fail" => route("payment_cancel"),
            ];

        return response()->json([
            'success' => true,
            'paypal' => $paypal,
            'toss' => $toss
            
        ]);

    }

    

}
