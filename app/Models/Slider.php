<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'url',
        'content',
        'slider_image'
    ];

    protected $casts = [
        'slider_image' => 'json', // Cast the profile_images attribute to an array
    ];

    // Ensure the model handles file uploads correctly
    protected function setSliderImageAttribute($value)
    {
        $this->attributes['slider_image'] = is_string($value) ? $value : json_encode($value);
    }

    public function getSliderUrl()
    {
        if ($this->slider_image) {
            // Check if post_thumbnail is already an array
            $sliderImage = is_array($this->slider_image) ? $this->slider_image : json_decode($this->slider_image, true);
    
            if ($sliderImage && is_array($sliderImage)) {
                $imageKey = reset($sliderImage);
                return Storage::disk('s3')->url($imageKey);
            }
        }
        return null;
    }
}
