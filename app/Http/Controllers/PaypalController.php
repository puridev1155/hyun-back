<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Payment;

class PaypalController extends Controller
{

    public function payment(Request $request) {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $price = 1;
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "brand_name" => "brand_name",
                "locale" => "en-US",
                  "return_url" => route("paypal_success", ['id' => 33]),
                  "cancel_url" => route("paypal_cancel")
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $price
                    ]
                ]
            ]
        ]);

        if(isset($response['id']) && $response['id'] != null ) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {   
                    return redirect()->away($links['href']);
                }
            }

            return redirect()->route('paypal_cancel');
        }

    }

    public function success(Request $request, $id) {

        $provider = new PaypalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request->token);

        if(isset($response['status']) && $response['status'] == 'COMPLETED') { 
            //결제 처리 완료
            $payment = Payment::where('id', $id)->first();
            $payment->update(['status' => 'paid']);
            return response()->json(['success' => true, 'message' => "Payment Is Successful!"]); 
        } else {
            return response()->json(['success' => false, 'message' => "Payment Is Cancel"]); 
        }
    }

    public function cancel()
    {
        return response()->json(['success' => false, 'message' => "Payment Is Cancel"]); 
    }
    
}
