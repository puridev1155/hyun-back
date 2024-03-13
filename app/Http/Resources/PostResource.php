<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Eprice;
use App\Http\Resources\EpriceResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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

        $data['created_by'] = $this->creator;
        $data['category'] = $this->category;
        $data['original_url'] = $this->getFirstMedia('post_image')->original_url?? "";
        $data['image_id'] = $this->getFirstMedia('post_image')->id?? "";
        $data['eprices'] = $this->eprices;
        $data['sns_agree'] = $this->sns_agree == 1 ? true : false;
        $data['time_agree'] = $this->start_date ? true : false; //table에 없는 컬럼. 필요해서 불러옴
        $data['price_agree'] = $this->price_agree == 1 ? true : false;
        $data['ticket_agree'] = $this->ticket_count ? true : false; //table에 없는 컬럼. 필요해서 불러옴
        $data['age_agree'] = $this->age_limit ? true : false; //table에 없는 컬럼. 필요해서 불러옴

        return $data;
    }
}
