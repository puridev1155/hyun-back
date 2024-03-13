<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['created_by'] = $this->creator->name??"";
        $data['image_url'] = $this->getFirstMedia('product_image')->original_url?? 'https://d2yu2kebcy91bk.cloudfront.net/228/default-product.jpg';
        $data['pprices'] = $this->pprices;

        return $data;
    }
}
