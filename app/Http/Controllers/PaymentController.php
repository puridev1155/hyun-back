<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
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

class PaymentController extends Controller
{
    public function payment(Request $request) {

        //$validated = $request->validated();
        $validated['id'] = $request->id;

        return $request;
        
        //TODO 더 간단하게 수정 그리고 데이터 불러오기 방법
        $payment = Payment::where('id', $validated['id'])->update($request->all());
        $payment = Payment::where('id', $validated['id'])->first();

        //TODO 코드 정리 //QR코드 등록
        QrcodeInfo::updateOrCreate([
            'model_type' => 'payment',
            'model_id' => $payment['id'],
            'post_id' => $payment['pay_id'],
            'user_id' => Auth::user()->id,
            'usage' => 0
        ]);     
    }

    public function success(Request $request) {

       $data = urldecode($request->id); //decodeUrl
       $dataArray = json_decode($data, true); // JSON 디코딩하여 PHP 배열로 변환합니다.

       if(is_array($dataArray)) {

            foreach($dataArray AS $item){

                $payment = Payment::where('user_id', Auth::user()->id)->where('id',  $item)->first();

                if($payment) {
                    $post = Post::find($payment->pay_id);    
                }
                
                if(isset($payment) && $post->ticket_count > $payment->amount) { //Ticket이 남은 경우
                    
                    $increment = $post->increment('buy_count', (int)$payment->amount);
                    $decrement = $post->decrement('ticket_count', (int)$payment->amount);
                }

            }
            
            //결제로 변경
            $payment = Payment::where('user_id', Auth::user()->id)->whereIn('id', $dataArray)->update([
                'status' => 'paid'
            ]);

            return response()->json(['success' => $payment ? true : false, 'message' => $payment ? 'Group Payment Is Successful!' : 'Cannot Find Records']); 
       } else if(isset($dataArray)) {
            //결제하기
            $payment = Payment::where('user_id', Auth::user()->id)->where('id', $dataArray)->first();
            $post = Post::find($payment->pay_id);

            if(isset($payment) && $post->ticket_count > $payment->amount) { //Ticket이 남은 경우

                $increment = $post->increment('buy_count', (int)$payment->amount);
                $decrement = $post->decrement('ticket_count', (int)$payment->amount);

                //결제로 변경
                $payment = $payment->update([
                    'status' => 'paid'
                ]);
                return response()->json(['success' => $payment ? true : false, 'message' => $payment ? 'Single Payment Is Successful!' : 'Cannot Find Record']);
            } else {
                return response()->json(['success' => false, 'message' => 'Ticket is not available.']);
            }
             
        } else {
            return response()->json(['success' => false, 'message' => "Payment Is Cancel"]); 
       }

       
    }

public function cancel()
{
    return response()->json(['success' => false, 'message' => "Payment Is Cancel"]); 
}


}
