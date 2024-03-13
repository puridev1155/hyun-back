<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Country;
use App\Models\Vote\Vote;
use App\Models\Qrcodeinfo;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasMedia
{
    //use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, InteractsWithMedia, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',//연락처
        'birth', //생년월일
        'gender',//성별 [1,2]
        'provider_name',//SNS 종류
        'provider_id',//SNS 아이디
        'country_id',//국가
        'referrer',//추천인
        'role',//admin:1, manager:5, (default)member: 10
        'address', //주소
        'zipcode', //우편번호
        'is_agree',//약관 동의
        'nickname',//별명
        'membership',//멤버쉽
        'block_notice',//공지 동의
        'height',//키
        'weight',//몸무게
        'lang_id',//언어종류
        'instagram',//인스타그램ID
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'user_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function qrcodeinfos(): HasMany
    {
        return $this->hasMany(Qrcodeinfo::class, 'user_id');
    }

    //User가 등록한 이미지
    public function getImgAttribute()
    {
        if ($this->hasMedia('profile_photo')) {
            $img = $this->getMedia('profile_photo')->last();
            return ['name' => $img['name'], 'url' => $img['preview_url'] ? $img['preview_url'] : $img['original_url']];
        }
        return null;
    }

    public function providers()
    {
        return $this->hasMany(Provider::class,'user_id','id');
    }

}
