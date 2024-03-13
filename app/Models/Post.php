<?php

namespace App\Models;

use App\Models\User;
use App\Models\Eprice;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $guarded = [];

    // protected $fillable = [
    //     'user_id',//사용자ID
    //     'country_id',//국가ID
    //     'category_id',//카테고리ID
    //     'eprice_id',//이벤트가격ID
    //     'title',//제목
    //     'order',//순서
    //     // 'board',//카테고리와 같은 역할
    //     'public',//공개 비공개 여부
    //     'content',//내용
    //     'like_count',//좋아요 수
    //     'view_count',//조회수
    //     'comment_count',//댓글수
    //     // 'participant_type',// 참여 여부
    //     'age_limit',//가능 나이
    //     // 'start_date' => nullable(); // Post 시작일
    //     // 'end_date' => nullable(); // Post 종료일
    //     // 'memo_info' => nullable();// 메모
    //     // 'memo_price' => nullable();// 가격종류 추가
    //     'ticket_count', // 티켓수
    //     // 'address' => nullable(); // 주소
    //     // 'address_detail' => nullable(); // 상세주소
    //     'sns_agree', //소셜 필수 사항
    //     'price_agree', //가격 필수 사항 (0:옵션, 1:필수, 3:무료)
    //     // 'x' => nullable(), // x 좌표
    //     // 'y' => nullable(), // y 좌표
    // ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function eprices(): HasMany
    {
        return $this->hasMany(Eprice::class, 'post_id', 'id');
    }


}
