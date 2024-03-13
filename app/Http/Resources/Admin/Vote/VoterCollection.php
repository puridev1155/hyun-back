<?php

namespace App\Http\Resources\Admin\Vote;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VoterCollection extends ResourceCollection //TODO 새롭게 생성
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return parent::toArray($request);
    }
}
