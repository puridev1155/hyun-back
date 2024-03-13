<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data['original_url'] = $this->getFirstMedia('product_image')->original_url?? 'https://d2yu2kebcy91bk.cloudfront.net/228/default-product.jpg';
        $data['image_id'] = $this->getFirstMedia('product_image')->id?? null;
        return [
            ...parent::toArray($request),
            'media' => null,
            'original_url' => $data['original_url'],
            'image_id' => $data['image_id']
        ];
        
    }
}
