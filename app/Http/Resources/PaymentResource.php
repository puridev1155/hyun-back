<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\MemberResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        $data['created_at'] = Carbon::make($this->created_at)->format("Y-m-d");
        $data['updated_at'] = Carbon::make($this->updated_at)->format("Y-m-d");

        $data['paid_by'] = $this->payer;

        return $data;
    }
}