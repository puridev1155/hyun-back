<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Statistic\UserStat;
use App\Models\Category;
use App\Models\Participant;
use App\Models\Payment;
use App\Http\Resources\ParticipantResource;
use App\Http\Resources\Admin\ParticipantCollection;
use App\Http\Controllers\Controller;

class DashBoardController extends Controller
{
    public function registerStatistics(Request $request)
    {
        // {
        //     "type": "year", (or "month","week" or "day")
        //     "dates": "2023-01-03,2023-09-05"
        // }

        $type = $request->type;
        $dates = explode(',', $request->dates);

        $registerChartData = UserStat::getChartData($type, $dates);

        return response()->json(compact('registerChartData'));
    }

    public function stats(Request $request) {  //TODO 새로 추가된 function participants에 대한 통계 가져오기


        $participant_count = Participant::selectRaw('category_id, COUNT(*) as count')
        ->groupBy('category_id')
        ->get();

        $status_count = Participant::selectRaw('payments.status, COUNT(*) as count')
        ->join('payments', 'participants.payment_id','=','payments.id')
        ->groupBy('payments.status')
        ->get();


        $total_count = Participant::count();
        
        return response()->json([$participant_count, $status_count, $total_count]);
    }

    public function statsEvent(Request $request) {  //TODO 새로 추가된 function participants에 대한 통계 가져오기


        $participant_count = Participant::selectRaw('category_id, COUNT(*) as count')
        ->groupBy('category_id')
        ->get();

        $status_count = Participant::selectRaw('payments.status, COUNT(*) as count')
        ->join('payments', 'participants.payment_id','=','payments.id')
        ->groupBy('payments.status')
        ->get();


        $total_count = Participant::count();
        
        return response()->json([$participant_count, $status_count, $total_count]);
    }
}
