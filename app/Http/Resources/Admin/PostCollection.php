<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);

        // return $this->collection->map(function ($post) {
        //     return [
        //         'id' => $post->id,
        //         'title' => $post->title,
        //         'content' => $post->content,
        //         'board' => $post->board,
        //         'category_id' => $post->category_id,
        //         // Add other attributes as needed

        //         // Include image data
        //         'image' => $post->getFirstMedia('post_image'),
        //     ];
        // })->toArray();
    }
}
