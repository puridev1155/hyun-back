<?php

namespace App\Http\Controllers\Admin\Statistics;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\Country;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ParticipantStatsController extends Controller
{
    public function index()
    {
        //전체 participant 수 (이벤트, 오디션, 티켓)
        $totalParticipant = Payment::where('payment_type', 'posts')->where('status', 'paid')->count();

        //전체 카테고리당 participant 수 (이벤트, 오디션, 티켓)
        $totalParticipantByCategory = Payment::leftJoin('posts', 'payments.pay_id', '=', 'posts.id')
                        ->where('payments.status', '=', 'paid')
                        ->select('posts.category_id', DB::raw('COUNT(*) as participant_count'))
                        ->groupBy('posts.category_id')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'category' => Category::find($item->category_id)->category_name,
                                'participant_count' => $item->participant_count,
                            ];
                        });

        // Print the final JSON
        return response()->json(compact('totalParticipant', 'totalParticipantByCategory'));
    }
}
