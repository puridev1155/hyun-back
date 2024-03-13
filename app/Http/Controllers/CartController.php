<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Admin\PaymentCollection;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\CartRequest;
use App\Exceptions\CustomException;

class CartController extends Controller
{
    public function index(Request $request)
    {
   
        // Fetch all posts
        $payment = Product::leftJoin('payments', 'products.id', '=', 'payments.pay_id')
        ->where('payments.status', 'hold')
        ->where('payments.user_id', Auth::user()->id)
        ->get();
        return CartResource::collection($payment);
    }

    public function store(CartRequest $request)
    {
        $validated = $request->validated();
        $product = Product::selectRaw("id, product_name")->where('id', $validated['pay_id'])->first();
        $validated['user_id'] = Auth::user()->id;
        $validated['payment_type'] = 'products';//고정 값
        $validated['title'] = $product->product_name;
        $payment = Payment::create($validated);

        return response()->json(['success' => true, 'message' => 'Product added to cart' ]);
    }


    public function update(CartRequest $request, $id)
    {
        $validated = $request->validated();
        $payment = Payment::findOrFail($id);
        // Update a specific post
        $payment->update($validated); 
        $validated['id'] = $payment['id'];
        return response()->json($validated);
    }

    public function payProducts(Request $request) {
        try {


        $ids = array(); // 빈 배열을 생성합니다.
        $total = 0;
        if($request->cart) {
            
            //결제 등록
            foreach($request->cart As $item) {
                Payment::where('id', $item['id'])->update(
                    ['status' => 'hold', 'amount' => $item['amount']
                    , 'address' => $request->address ? $request->address : null, 'zipcode' => $request->zipcode ? $request->zipcode : null ]);
                
                    $pay = Payment::where('id', $item['id'])->first();
                    $ids[] = $pay->id;
                    $total += (int)$pay->price * (int)$pay->amount;
        
                }
            
            //상품 array로 받아오기
            $payment = Payment::selectRaw("title AS name, CAST(amount AS SIGNED) AS quantity, CAST(price AS CHAR) As price, 'USD' as currency")->WhereIn('id', $ids)->get();
                
            $url = json_encode($ids);
            $paypal = [
                'client_id' =>  env('PAYPAL_SANDBOX_CLIENT_ID'),
                'secret_id' =>  env('PAYPAL_SANDBOX_CLIENT_SECRET'),
                'total' => strval($total),
                'currency' => 'USD',
                'subtotal' => strval($total),
                'description' => '구매' . count($ids) .'종류-(' . implode(',', $ids) .')',
                'items' =>  $payment,
                'shipping_address' => [
                    'recipient_name' => '이름',
                    'line1' => null,
                    'phone' => '01011113333'
                ],
                "return_url" => route("payment_success", ['id' => urlencode($url)]),
                "cancel_url" => route("payment_cancel"),
                
            ];
    
            $toss = [
                'success' => true,
                'clientKey' =>  env('TOSS_CLIENT_ID'),
                'data' => [
                    'paymentMethod'=> '카드',
                    'orderId' => 'tosspayments-(' . implode(',', $ids) .')',
                    "orderName" => '구매' . count($ids) .'종류-(' . implode(',', $ids) .')',
                    'amount' => $total,
                    'customerName' => '이름',
                    'customerEmail' => null,       
                ],
                "success" => route("payment_success", ['id' => urlencode($url)]),
                "fail" => route("payment_cancel"),
                ];
    
            return response()->json([
                'success' => true,
                'paypal' => $paypal,
                'toss' => $toss
                
            ]);


            return response()->json(['success' => true]);
        } } catch (CustomException $e) {
            // Category with the given ID was not found
            return response()->json(['error' => 'Array Error'], 404);
        }
    }

    public function destroy($id)
    {
        // Delete a specific product
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return response()->noContent();
    }
}
