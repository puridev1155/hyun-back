<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get the parent's array representation
        $data = parent::toArray($request);

        $data['created_at'] = Carbon::make($this->created_at)->format("Y-m-d");
        $data['updated_at'] = Carbon::make($this->updated_at)->format("Y-m-d");

        $data['post'] = $this->post;


        return $data;
    }
}
