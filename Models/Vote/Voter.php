<?php

namespace App\Models\Vote;

use App\Models\User;
use App\Models\Vote\Vote;
use App\Models\Vote\Choice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voter extends Model
{

    protected $guarded = [];

    use HasFactory;

    public function vote(): BelongsTo
    {
        return $this->belongsTo(Vote::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function choice(): BelongsTo
    {
        return $this->belongsTo(Choice::class);
    }
}
