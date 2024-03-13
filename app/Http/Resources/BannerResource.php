<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['image_url'] = $this->getFirstMedia('banner_image')->original_url?? "";
        $data['image_id'] = $this->getFirstMedia('banner_image')->id?? "";
        // $data['category'] = new CategoryResource($this->category);

        return $data;
    }
}
