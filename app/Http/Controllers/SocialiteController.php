<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SocialiteController extends Controller
{
    public function handleProviderCallback(Request $request)
    {

        try {
            //$user = Socialite::driver('kakao')->stateless()->user();
            //return [$user->token];
            Log::debug(['token' => $request->access_provider_token]);
            
            $validator = [
                'provider' => $request->provider,
                'access_provider_token' => $request->access_provider_token
            ];

            $user = Socialite::driver($validator['provider'])->stateless()->userFromToken($validator['access_provider_token']);
            Log::debug([$user]);

            Log::debug(['email' => $user->email]);
        if($validator['provider'] == 'kakao') {
            $getUser = User::where('email', $user->email)->first();
        } if($validator['provider'] == 'apple') {
            $getUser = User::where('email', $user->email )->first();
        } if($validator['provider'] == 'google') {
            Log::debug(['google' => 'success']);
            $getUser = User::where('email', strval($user->email))->first();
            Log::debug(['getUser' => $getUser]);
        } else {
            $getUser = User::where('email', $user->email)->first();
            //$getUser = User::where('email', $user->getEmail())->first();
        }


        
        if(!$getUser) {
            Log::debug(['success' => '회원 못찾음']);
            return response()->json(['success' => false, 'message' => 'Failed', 'token' => null]);
        }

        Log::debug(['provider_id' => $user->token]);
        $providerUpdate = Provider::updateOrCreate([
            'provider' => $validator['provider'],
            'provider_id' => $user->token,
            'user_id' => $getUser->id,
        ]);

        $token = $getUser->createToken('auth-token')->plainTextToken;
            $parts = explode('|', $token);
            $token = $parts[1];

            $data = [
                'token' => $token
            ];

            Log::debug(['token' => $data, 'data' => '마지막 단계']);   

            return response()->json(['success' => true, 'message' => 'Successful', 'token' => $data['token']]);


        } catch (ClientException $e) {
            // You can log the error for debugging purposes
            //Log::error($e);

            // Return a failure response
            return response()->json(['success' => false, 'message' => 'Failed', 'token' => null]);
        }


    }

    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['google'])) {
            return response()->json(["message" => 'You can only login via google account']);
        }
    }
}