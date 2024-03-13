<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membership;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
      
    $membership = Membership::latest()->first;

    return $membership;

    }

    public function topCarousel(Request $request) {


            $events = Post::selectRaw('posts.id, posts.lang_id, posts.title, posts.title, posts.category_id, posts.memo_price, posts.memo_info');
            if($request->category) {
                $events = $events->where('category_id', $request->category);
            }
            $events = $events->where('board', 'event')->latest()->limit(6)->get();
            $events = PostPageResource::collection($events);
            return response()->json($events);
    } 


    // 티켓 상세 페이지 (App9)
    // (티케 썸네일 이밎, title, ticket_type, available_ticket, cost, content)
    // (베너 하단)
    public function show(Request $request, Post $audition)
    {
        return new AuditionResource($audition);
    }

    public function store(PostRequest $request) {
        $validated = $request->validated();
        $post = Post::selectRaw("id, title")->where('id', $validated['pay_id'])->first();
        $user = User::findOrFail(Auth::user()->id);
        $validated['user_id'] = $user->id;
        $validated['payment_type'] = 'posts';//고정 값
        $validated['title'] = $post->title;
        $payment = Payment::create($validated);

        //TODO 코드 정리 //QR코드 등록
        if($payment['status'] == 'paid') {
            QrcodeInfo::create([
                'model_type' => 'payment',
                'model_id' => $payment['id'],
                'post_id' => $payment['pay_id'],
                'user_id' => Auth::user()->id,
                'usage' => 0
            ]) ;
        } 


        return response()->json($payment);

    }
}
