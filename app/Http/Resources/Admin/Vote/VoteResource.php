<?php

namespace App\Http\Resources\Admin\Vote;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Admin\Vote\ChoiceResource;

class VoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        // $data['vote_image'] = $this->getFirstMedia('vote_image');
        $data['original_url'] = $this->getFirstMedia('vote_image')->original_url?? "";
        $data['start_at'] = Carbon::parse($this->start_at)->format('Y-m-d');
        $data['end_at'] =Carbon::parse($this->end_at)->format('Y-m-d');

        $data['choices'] = ChoiceResource::Collection($this->choices);

        // $data['voted_by'] = $this->voters()->where('user_id', auth()->id())->first();

        return $data;
    }
}
