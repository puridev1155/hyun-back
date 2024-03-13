<?php

namespace App\Listeners;

// use App\Events\MemberRegistered;
use App\Models\Statistic\UserStat;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateMemberStat
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if($event->type === "new_count") {
            UserStat::updateOrInsert(
                ['country_id' => $event->user->country_id, 'date' => now()->format('Y-m-d')],
                ['new_count' => DB::raw("IFNULL(new_count, 0) + 1")]
            );
        }

        if($event->type === "withdrawn_count") {
            UserStat::updateOrInsert(
                ['country_id' => $event->user->country_id, 'date' => now()->format('Y-m-d')],
                ['withdrawn_count' => DB::raw("IFNULL(new_count, 0) + 1")]
            );
        }
    }
}
