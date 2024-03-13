<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Vote\Voter;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class VotePageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data['original_url'] = $this->getFirstMedia('vote_image')->original_url?? 'https://d2yu2kebcy91bk.cloudfront.net/220/default.jpg';
        $data['image_id'] = $this->getFirstMedia('vote_image')->id?? null;
        $result = 1;
        $message = 'success';
        
        //이미 투표한 경우
        $voter = Voter::where('user_id', Auth::user()->id)->where('vote_id', $this->id)->first();
        if($voter) {
            $result = 0;
            $message = 'already';
        }

        if($this->require_id) { 
            $payment = Payment::where('user_id', $this->user_id)->where('pay_id', $this->require_id)->where('status', 'paid')->first();
            if(!$payment) { // 결제하였는데 투표안한 경우
                $result = 0;
                $message = 'buy';
            }
        }
        

        return [
            ...parent::toArray($request),
            'media' => null,
            'original_url' => $data['original_url'],
            'image_id' => $data['image_id'],
            'result' => $result,
            'message' => $message,
        ];
        
    }
}
