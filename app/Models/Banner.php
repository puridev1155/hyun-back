<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'url',
        'content',
        'banner_image'
    ];

    protected $casts = [
        'banner_image' => 'json', // Cast the profile_images attribute to an array
    ];

    // Ensure the model handles file uploads correctly
    protected function setBannerImageAttribute($value)
    {
        $this->attributes['banner_image'] = is_string($value) ? $value : json_encode($value);
    }

    public function getBannerUrl()
    {
        if ($this->banner_image) {
            // Check if post_thumbnail is already an array
            $bannerImage = is_array($this->banner_image) ? $this->banner_image : json_decode($this->banner_image, true);
    
            if ($bannerImage && is_array($bannerImage)) {
                $imageKey = reset($bannerImage);
                return Storage::disk('s3')->url($imageKey);
            }
        }
        return null;
    }
}
