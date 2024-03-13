<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\User;
use App\Models\Payment;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Resources\ShopResource;
use App\Http\Resources\Admin\ProductCollection;
use App\Http\Resources\ProductPageResource;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\GetProductRequest;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class ProductController extends Controller
{
    public function init(Request $request) {
        $data = Category::where('category_type', 'product')->get();

        return response()->json($data);
    }

    public function index(Request $request)
    {
        $itemsPerPage = $request->itemsPerPage ? $request->itemsPerPage : 10;

        $lang = $request->lang_id ? $request->lang_id : 1;

        $products = QueryBuilder::for(Product::class)
        ->selectRaw('id, brand, category_id, product_name, memo_price, brand')
        ->where('lang_id', $lang)
        ->allowedFilters([
            'product_name', //제목,
            'category_id'
            ])
        ->defaultSort('-created_at')
        ->paginate($itemsPerPage);

        $products = ProductPageResource::collection($products);    
        return  $products;   

        //if (Cache::has('products')) {
           // $products = Cache::get('products');
        //} else {
            // filter (in url): ?filter[key]=value&filter[key]=value...
            // sort (in url): ?sort=key, key... (decending if -key)
        //    $products = QueryBuilder::for(Product::class)
        //    ->selectRaw('id, brand, category_id, product_name, memo_price, brand')
        //    ->where('lang_id', $lang)
        //    ->allowedFilters([
        //        'product_name', //제목,
        //        'category_id'
        //        ])
        //    ->defaultSort('-created_at')
        //    ->paginate($itemsPerPage);    
           // Cache::put('products', $products, 1);

        //}
    }

    public function popular(Request $request) {    


        $lang = $request->lang_id ? $request->lang_id : 1;
        $populars = QueryBuilder::for(Product::class)
        ->selectRaw('id, brand, category_id, product_name, memo_price')
        ->where('lang_id', $lang)
        ->allowedFilters([
            'category_id'
            ])
        ->defaultSort('-out_stock')->limit(6)->get();
        $populars =  ProductPageResource::collection($populars);
        return response()->json($populars);
         
        //if (Cache::has('populars')) {
           // $populars = Cache::get('populars');
        //} else {
            // filter (in url): ?filter[key]=value&filter[key]=value...
            // sort (in url): ?sort=key, key... (decending if -key)
        //    $populars = QueryBuilder::for(Product::class)
        //    ->selectRaw('id, brand, category_id, product_name, memo_price')
        //    ->where('lang_id', $lang)
        //    ->allowedFilters([
        //        'category_id'
        //        ])
        //    ->defaultSort('-out_stock')->limit(6)->get();
           // Cache::put('populars', $populars, 1);
        //}

    }

    public function show(Request $request, Product $product)
    {
        unset($product->in_stock,$product->out_stock, $product->created_at, 
        $product->updated_at, $product->updated_at, $product->memo_price, $product->user_id);

        return new ShopResource($product);
    }

    public function cart(Request $request, Product $product)
    {
        return new ShopResource($product);
    }

    public function store(ProductRequest $request)
    {
        $validated = $request->validated();
        $product = Product::selectRaw("id, product_name")->where('id', $validated['pay_id'])->first();
        $user = User::findOrFail(Auth::user()->id);
        $validated['user_id'] = $user->id;
        $validated['payment_type'] = 'products';//고정 값
        $validated['title'] = $product->product_name;
        $payment = Payment::create($validated);

        $payment['original_url'] = $product->getFirstMedia('product_image')->original_url?? 'https://d2yu2kebcy91bk.cloudfront.net/220/default.jpg';
        $payment['description'] = $product->description;
        $payment['name'] = $user->name;

        return response()->json($payment);

    }

    public function update(ProductRequest $request) {
         $validated = $request->validated();

        //TODO 내용 업데이트
        $validated['status'] = "hold";

        $payment = Payment::where('id', $validated['id'])->update($validated);
        $payment = Payment::where('id', $validated['id'])->first();
        $user = User::findOrFail(Auth::user()->id);

        $paypal = [
            'client_id' =>  env('PAYPAL_SANDBOX_CLIENT_ID'),
            'secret_id' =>  env('PAYPAL_SANDBOX_CLIENT_SECRET'),
            'data' => [
                'total' => strval($payment['price'] * (int)$payment['amount']),
            'currency' => 'USD',
            'subtotal' => strval($payment['price'] * (int)$payment['amount']),
            'description' => '백에드에서 상품 내용 들어가게 설정해놓겠습니다',
            'items' => [
                [ 
                    'name' => $payment['title'],
                    'quantity' => $payment['amount'], // Provide a quantity
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
            ]
            
            
        ];

        $toss = [
            'clientKey' =>  env('TOSS_CLIENT_ID'),
            'data' => [
                'paymentMethod'=> '카드',
                'orderId' => 'tosspayments-' . $payment['id'],
                "orderName" => $payment['title'],
                'amount' => $payment['price'] * (int)$payment['amount'],
                'customerName' => $user->id . '_' . $user->name,
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
