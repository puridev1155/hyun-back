<?php

namespace App\Http\Controllers\Admin\Statistics;

use Carbon\Carbon;
use App\Models\Vote\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Statistic\Vstatistic;

class VstatisticsController extends Controller
{
    public function index()
    {
        $votesInfo = json_decode(Vstatistic::select("*")->get(), true);

        // Create the new data structure
        $countPerChoice = [];
        foreach ($votesInfo as $vote) {
            $voteId = $vote['vote_id'];
            $voteTitle = $vote['vote_title'];
            $choice = [
                'name' => $vote['name'],
                'counts' => $vote['total_voters'],
            ];

            if (!isset($countPerChoice[$voteId])) {
                $countPerChoice[$voteId] = [
                    'vote_id' => $voteId,
                    'vote_title' =>$voteTitle,
                    'choices' => [],
                ];
            }

            $countPerChoice[$voteId]['choices'][] = $choice;
        }

        // Convert the transformed data to a simple array format
        $overallVoteResult = array_values($countPerChoice);

        //Vote 진행중
        $activeVotes = Vote::where('end_at', '>', Carbon::now())->count();

        //vote 종료
        $endedVotes = Vote::where('end_at', '<=', Carbon::now())->count();

        //vote unlimited
        $unlimitedVotes = Vote::whereNull('end_at')->count();

        //전체 투표수
        $totalVote = Vote::count();

        //투표당 참여자수
        $votersPerVote = Vstatistic::select('vote_id', DB::raw('SUM(total_voters) as total_voters'))
                            ->groupBy('vote_id');

        $getVotersPerVote = $votersPerVote->get();

        //가장 많이 참여한 투표
        $mostPopularVote = $votersPerVote->orderByDesc('total_voters')->first();

        $getMostPopularVote = Vote::find($mostPopularVote->vote_id);
        $getMostPopularVote['total_voters'] = $mostPopularVote->total_voters;


        // Print the final JSON
        return response()->json(compact('overallVoteResult', 'totalVote', 'getVotersPerVote', 'getMostPopularVote',
                            'activeVotes', 'endedVotes', 'unlimitedVotes'));
    }
}
