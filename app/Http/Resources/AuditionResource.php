<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data['id'] = $this->id;
        $data['title'] = $this->title;
        $data['content'] = $this->content;
        $data['description'] = $this->memo_info ?? null;
        $data['category_id'] = $this->category_id;
        $data['original_url'] = $this->getFirstMedia('post_image')->original_url?? 'https://d2yu2kebcy91bk.cloudfront.net/220/default.jpg';
        $data['currency'] = '₩';
        $data['image_id'] = $this->getFirstMedia('post_image')->id?? null;
        $data['eprices'] = $this->eprices ? $this->eprices : null;
        $data['post_type'] = $this->post_type;
        return $data;
    }
}
