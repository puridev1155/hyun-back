<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['image_url'] = $this->getFirstMedia('product_image')->original_url?? 'https://d2yu2kebcy91bk.cloudfront.net/228/default-product.jpg';
        return $data;
    }
}
