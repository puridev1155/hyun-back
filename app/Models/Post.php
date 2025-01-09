<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'category_id',
        'title',
        'content',
        'years',
        'clients',
        'specs',
        'dates',
        'post_thumbnail',
        'post_images'
    ];

    protected $casts = [
        'post_images' => 'json', // Cast the profile_images attribute to an array
        'post_thumbnail' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = 1;
        });

        static::saving(function ($model) {
            $model->user_id = 1;
        });
    }

    // Ensure the model handles file uploads correctly
    protected function setPostThumbnailAttribute($value)
    {
        $this->attributes['post_thumbnail'] = is_string($value) ? $value : json_encode($value);
    }

    protected function setPostImagesAttribute($value)
    {
        $this->attributes['post_images'] = is_string($value) ? $value : json_encode($value);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getThumbnailUrl()
    {
        if ($this->post_thumbnail) {
            // Check if post_thumbnail is already an array
            $postThumbnail = is_array($this->post_thumbnail) ? $this->post_thumbnail : json_decode($this->post_thumbnail, true);
    
            if ($postThumbnail && is_array($postThumbnail)) {
                $imageKey = reset($postThumbnail);
                return Storage::disk('s3')->url($imageKey);
            }
        }
        return null;
    }
}
