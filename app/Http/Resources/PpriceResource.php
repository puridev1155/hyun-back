<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PpriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        // $data['created_at'] = Carbon::make($this->created_at)->format("Y-m-d");
        // $data['updated_at'] = Carbon::make($this->updated_at)->format("Y-m-d");

        $data['product'] = $this->product;


        return $data;
    }
}
