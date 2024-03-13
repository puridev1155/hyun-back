<?php

namespace App\Models\Vote;

use App\Models\User;
use App\Models\Vote\Voter;
use App\Models\Vote\Choice;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vote extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];


    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function voters(): HasMany
    {
        return $this->hasMany(Voter::class);
    }

    // public function getImgAttribute()
    // {
    //     if($this->hasMedia('thumbnail')) {
    //         $media = $this->getMedia('thumbnail')->last();
    //         // ["preview_url"]

    //         return [
    //             "name" => $media->file_name,
    //             "url" => $media->getFullUrl(),
    //         ];
    //     }

    //     return null;
    // }

}
