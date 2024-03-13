<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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

        // Add additional data to the array from Country model
        $data['country'] = $this->country;
        $data['media'] = null;

        return $data;
    }
}
