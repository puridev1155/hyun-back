<?php

namespace App\Models;

use App\Models\Banner;
use App\Models\Notice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'catecode', 'category_type', 'category_name', 'category_parent'
    ];

    protected $hidden =[
        'created_at', 'updated_at'
    ];

    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class, 'category_id');
    }

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class, 'category_id');
    }

}
