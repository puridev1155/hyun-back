<?php

namespace App\Http\Resources;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data['original_url'] = $this->getFirstMedia('post_image')->original_url?? 'https://d2yu2kebcy91bk.cloudfront.net/220/default.jpg';
        $data['image_id'] = $this->getFirstMedia('post_image')->id?? null;
        return [
            ...parent::toArray($request),
            'media' => null,
            'original_url' => $data['original_url'],
            'image_id' => $data['image_id'],
            'memo_price' => $this->memo_price ? $this->memo_price : "Free"
        ];
        
    }
}
