<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\vote\Voter;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Country;
use App\Models\Account;
use App\Models\Membership;
use Illuminate\Support\Str;
use App\Models\PasswordReset;
use App\Events\MemberRegistered;
use App\Events\MemberWithdrawned;
use App\Exceptions\JsonException;
use App\Exceptions\CustomException;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\SellPageResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Auth\UserRequest;
use App\Http\Requests\AlertRequest;
use App\Models\Qrcodeinfo;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {

        try {

            $request->authenticate();

            //FOR SESSION BASE LOGIN (Only Web)
            $request->session()->regenerate();
            //FOR SESSION BASE LOGIN (Only Web)

            $token = $request->user()->createToken('auth-token')->plainTextToken;
            $parts = explode('|', $token);
            $token = $parts[1];
            Log::debug(['token' => $token]);

            $data = [
                'token' => $token
            ];

            return response()->json(['success' => true, 'message' => 'Successful', 'token' => $data['token']]);

        } catch (ClientException $e) {
            // You can log the error for debugging purposes
            //TODO 이것이 왜 안되는지 확인 필요 
            return response()->json(['success' => false, 'message' => 'Failed', 'token' => null]);
        }



    }

    public function register(UserRequest $request)
    {

        $validated = $request->validated();

        if(User::where('email', $validated['email'])->exists()) {
            throw new JsonException('email has been already used', 200);
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $token = $user->createToken('auth-token')->plainTextToken;
        $parts = explode('|', $token);
        $token = $parts[1];

        //event(new MemberRegistered($user));

        return response()->json([
            'success' => true,
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);

    }

    //forget password
    public function forgetPassword(Request $request)
    {
        try{

            $user = User::where('email', $request->email)->get();

            if(count($user) > 0){
                $token = Str::random(6);
                $domain = URL::to('/api/');
                $url = $domain.'/reset-password?token='.$token;
                $data['url'] = $url;
                $data['email'] = $request->email;
                $data['title'] = "Password Reset";
                $data['token'] = $token;

                Mail::send('forgetPasswordMail', ['data'=>$data], function($message) use ($data){
                    $message->to($data['email'])->subject($data['title']);
                });

                $datetime = Carbon::now()->format('Y-m-d H:i:s');

                PasswordReset::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'email' => $request->email,
                        'token' => $token,
                        'created_at' => $datetime
                    ]
                    );
                return response()->json([
                    'success' => true,
                    'msg'=>'Insert Below Code',
                    'code'=> $token
                ]);

            } else {

                return response()->json(['success'=>false,'msg'=>'Enter the wrong email address!']);
            }

        } catch (\Exception $e) {
            return response()->json(['success'=>false, 'msg'=>$e->getMessage()]);
        }
    }

    public function emailVerify(Request $request){
        try{      

            if($request->token) {
                $resetData = PasswordReset::where('token', $request->token)->get();
                    if(isset($request->token) && count($resetData)>0){
                        return response()->json(['success'=>true,'message'=>'Certified']); //add input certify
                    
                    } else {
                        return response()->json(['success'=>false,'message'=>'Enter the wrong code. Try again.']);
                    }

            }
            
            
            $email = User::where('email', $request->email)->first();
            
            if($email) {
                return response()->json(['success'=>false,'message'=>'Email is already used. Try again.']);
            }

            $token = Str::random(6);
            $domain = URL::to('/api/');
            $url = $domain.'/email-vertify?token='.$token;
            $data['url'] = $url;
            $data['email'] = $request->email;
            $data['title'] = "Email Certify";
            $data['token'] = $token;

            Mail::send('EmailVerification', ['data'=>$data], function($message) use ($data){
                $message->to($data['email'])->subject($data['title']);
            });

                $datetime = Carbon::now()->format('Y-m-d H:i:s');

                PasswordReset::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'email' => $request->email,
                        'token' => $token,
                        'created_at' => $datetime
                    ]
                    );
                return response()->json([
                    'success' => true,
                    'message'=>'Token is sent to email.',
                    'code'=> $token
                ]);

        } catch (\Exception $e) {
            return response()->json(['success'=>false, 'message'=>$e->getMessage()]);
        }
    }

    public function resetPasswordLoad(Request $request)
    {
        $resetData = PasswordReset::where('token', $request->token)->get();
        if(isset($request->token) && count($resetData)>0){

            $user = User::where('email', $resetData[0]['email'])->get();
            return response()->json(['success'=>true,'msg'=>'User Found!']); //add input password in view

        } else {
            return response()->json(['success'=>false,'msg'=>'Enter the wrong code. Try again.']);
        }

    }


    public function resetPassword(Request $request)
    {
        $resetData = PasswordReset::where('token', $request->token)->first();

        if($resetData){

            $user = User::where('email', $resetData['email'])->first();
            $user->password = $request->password;
            $user->save();

            return response()->json(['success'=>true,'msg'=>'Your password has been reset']);

        } else {
            return response()->json(['success'=>false,'msg'=>'Invalid Token. Try again.']);
        }
    }

    public function myResetPassword(Request $request) {
        $user = User::where('email', Auth::user()->email)->first();
        $user->password = $request->password;
        $user->save();
        return response()->json(['success'=>true,'msg'=>'Your password has been reset']);

    }

    public function show(Request $request)
    {

        $user = User::findOrFail(Auth::user()->id);

        $profile = $user->getMedia('profile_image')->last();
        if($profile) {
            $image = $profile->original_url ? $profile->original_url : null;
        } else {
            $image = null;
        }
        
        if($request->address) { //주소 찾기
            return response()->json([ 'success' => $user->address ? true : false, 
            'zipcode' => $user->zipcode ? $user->zipcode : null, 'address' => $user->address ? $user->address : null]);
        }
        if($request->phone) { //연락처 찾기
                return response()->json([ 'success' => $user->phone ? true : false, 
                'phone' => $user->phone]);
        } 

        if($request->paypal_id) { //페이팔 아이디 찾기
            $account = Account::where('user_id', Auth::user()->id)->latest()->first();
            if($account) {
                return response()->json([ 'success' => $account->paypal_id ? true : false, 
                'paypal_id' => $account->paypal_id]);
            } else {
                return response()->json([ 'success' => false, 
                'paypal_id' => null]);
            }

        }

        if($request->bank_account) {
            $account = Account::where('user_id', Auth::user()->id)->latest()->first();
            if($account) {
                return response()->json([ 'success' => $account->bank_account ? true : false, 
                'bank_account' => $account->bank_account ]);
            } else {
                return response()->json([ 'success' => false, 
                'bank_account' => null]);
            }
        }

        return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'country_id' => $user->country_id,
        'lang_id' => $user->lang_id,
        'birth' => $user->birth,
        'gender' => $user->gender,
        'height' => $user->height,
        //앱에서는 role을 주석처리해야 함;
        'role' => $user->role,
        'instagram' => $user->instagram,
        'address' => $user->address,
        'zipcode' => $user->zipcode,
        'phone' => $user->phone,
        'profile_url' => $image ]);



    }


    public function showProfile(Request $request)
    {

        $user = User::findOrFail(Auth::user()->id);
        $profile = $user->getMedia('profile_image')->last();
        if($profile) {
            $image = $profile->original_url ? $profile->original_url : 'https://d2yu2kebcy91bk.cloudfront.net/313/7022927.png';
        } else {
            $image = 'https://d2yu2kebcy91bk.cloudfront.net/313/7022927.png';
        }
        return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'instagram' => $user->instagram,
        'profile_url' => $image ]);
    }

    public function updateProfile(UserRequest $request)
    {
        $data = $request->validated();
        $user = User::findOrFail(Auth::user()->id);

        if($request->file('profile_url')) {
            $user->clearMediaCollection('profile_image');
            $profile = $user->addMedia($request->file('profile_url'))->toMediaCollection('profile_image', 's3');
        }

        $save = User::where('id', $user->id)->update($data); 
        $profile = $user->getMedia('profile_image')->last();
        
        if($profile) {
            $image = $profile->original_url ? $profile->original_url : 'https://d2yu2kebcy91bk.cloudfront.net/313/7022927.png';
        } else {
            $image = 'https://d2yu2kebcy91bk.cloudfront.net/313/7022927.png';
        }

        $data['profile_url'] = $image;
        $data['email'] = $user->email;


        $store =  new UserResource($data);
        return response()->json(['success' => true,
        'data' => $store]);
    }

    public function storeImage(Request $request) {

        $user = User::findOrFail(Auth::user()->id);
        $user->clearMediaCollection('profile_image');

        if ($request->file('image')) {
            $profile = $user->addMedia($request->file('image'))->toMediaCollection('profile_image', 's3');
        } else {
            return response()->json(['code' => 'success', 'data' => null]);
        }

        return response()->json(['code' => 'success', 'data' => $profile->original_url]);
    }

    public function destroyProfile()
    {
        event(new MemberWithdrawned(auth()->user()));
        auth()->user()->delete();
        return $this->respondSuccessfully();
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        auth()->guard('web')->logout();
        // $request->session()->invalidate();
        // $request->session()->regenerate();

        return response()->json(['success' => true ]);
    }

    public function delete(Request $request) //user 삭제
    {
        $user = User::findOrFail(Auth::user()->id);
        //$user->delete();
        $user->forceDelete();
        return response()->noContent();
    }


    public function paymentHistory(Request $request)
    {
        $payment = Payment::
        selectRaw('id, title, price, status, created_at')
        ->where('user_id', Auth::user()->id);

        if($request->status) {
            $payment = $payment->where('status', $request->status);
        }
        $payment = $payment->get();

        return response()->json($payment);
    }

    public function sellHistory(Request $request)
    {
        if(Auth::user()->role)  {
            $itemsPerPage = $request->itemsPerPage ? $request->itemsPerPage : 10;
            $payment = Payment::
            leftJoin('posts', 'payments.pay_id', '=', 'posts.id')
            ->leftJoin('users', 'payments.user_id', '=', 'users.id')
            ->selectRaw('payments.id, users.name, payments.title,  payments.amount, payments.price, payments.amount * payments.price AS total, payments.updated_at, payments.status')
            ->where('posts.user_id', Auth::user()->id)
            ->paginate($itemsPerPage);

            $subtotal = 20000;
            $total = $payment->count();
            return SellPageResource::collection($payment)->additional([ 'subtotal' => $subtotal, 'total' => $total]);
        } else {
            return response()->json(['success' => false, 'message' => 'You are not a seller.']);
        }
        

    }

    public function eventHistory(Request $request)
    {
        $payment = Payment::leftJoin('posts', 'payments.pay_id', '=', 'posts.id')
        ->selectRaw('payments.id, posts.category_id, payments.title, payments.updated_at, payments.status, payments.qrcode, payments.pay_id')
        ->where('payments.user_id', Auth::user()->id);
		

        if($request->category_id == 1 || $request->category_id == 2) { //오디션 1, 티켓 2
            $payment = $payment->where('posts.category_id', $request->category_id);
        } else if($request->category_id == 3) {
            $payment = Voter::selectRaw('id, choice_id AS category_id, vote_title AS vote_title, 
            updated_at, "paid" AS status, "1" AS qrcode, vote_id AS pay_id')->where('user_id', Auth::user()->id);
        }

        $payment = $payment->get();

        return response()->json($payment);
    }

    public function storeMembership(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        if($user['membership'] == 1) {
            $user = tap($user)->update([
                'membership' => 0
            ]);
        } else {
            $user = tap($user)->update([
                'membership' => 1
            ]);
        }

        return response()->json([
            'message' => 'success',
             'data' => $user['membership']]);
    }

    public function membership(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        return response()->json([
            'message' => 'success',
             'data' => $user['membership']]);
    }



    public function countryList(Request $request) {

        $data = Country::selectRaw('id, name')->get();

        return response()->json($data);
    }

    public function alert(Request $request) {
        $user = User::findOrFail(Auth::user()->id);

        return response()->json([
            'alert' => $user['alert_agree'],
            'marketing' => $user['marketing_agree']
        ]);
    }

    public function lang(Request $request) {
        $user = User::findOrFail(Auth::user()->id);

        return response()->json([
            'lang_id' => $user['lang_id'],
        ]);
    }

    public function updateAlert(AlertRequest $request) {

        $validated = $request->validated();
        $user = User::where('id', Auth::user()->id)->update($validated);
        $user = User::findOrFail(Auth::user()->id);

        return response()->json([
            'message' => 'success',
            'alert' => $user['alert_agree'],
            'marketing' => $user['marketing_agree']

        ]);
    }

    public function updateLang(Request $request) {
        $validated['lang_id'] = $request->lang_id;
        $user = User::findOrFail(Auth::user()->id);
        $user = tap($user)->update($validated);
        return response()->json([
            'message' => 'success',
            'lang_id' => $user['lang_id'],
        ]);
    }

    public function adminPayment(Request $request) {
        $post = Payment::selectRaw('payments.id, users.name, payments.created_at, payments.price, payments.status')
        ->leftJoin('posts', 'payments.pay_id', '=', 'posts.id')
        ->leftJoin('users', 'payments.user_id', '=', 'users.id')
        ->where('posts.user_id', Auth::user()->id)->get();
        $product = Payment::selectRaw('payments.id, users.name, payments.created_at, payments.price, payments.status')
        ->leftJoin('products', 'payments.pay_id', '=', 'products.id')
        ->leftJoin('users', 'payments.user_id', '=', 'users.id')
        ->where('products.user_id', Auth::user()->id)->get();

        $payments = $post->concat($product); // two array merge

        return response()->json($payments);
    }

    public function agreement(Request $request) {
        $lang = $request->lang_id ? $request->lang_id : 1;
        if($lang) {
            $agree = DB::table('agreements')->where('lang_id', $lang)->get();
            return response()->json($agree);
        }

    }


}
