<?php

namespace App\Http\Controllers;

use App\Models\Vote\Vote;
use App\Models\Vote\Voter;
use App\Models\Vote\Choice;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\VoterResource;
use App\Http\Resources\VotePageResource;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Requests\Admin\Vote\VoterRequest;
use App\Http\Resources\Admin\Vote\VoteResource;
use App\Http\Resources\Admin\Vote\VoteCollection;
use App\Http\Resources\Admin\Vote\VoterCollection;
use Illuminate\Support\Facades\Log;

class VoteController extends Controller
{
    public function index(Request $request)
    {
        // vote 최신 리스트 전체 불러오기 [날짜에 맞춰서 진행중, 종료 분별해서 보내주기]
        // (썸네일, title)
        $itemsPerPage = $request->itemsPerPage ? $request->itemsPerPage : 10;
        $lang = $request->lang_id ? $request->lang_id : 1;

        $votes = QueryBuilder::for(Vote::class)
        ->selectRaw('votes.id, votes.title, votes.require_id,? AS user_id', [Auth::user()->id])
        ->defaultSort('-created_at')
        ->paginate($itemsPerPage);

        return VotePageResource::collection($votes);
    }

    public function show(Request $request, Vote $vote)
    {
        try {
            Log::debug(['message' => 'vote 단계', 'data' => $vote ]);
            unset($vote->start_at,$vote->end_at, $vote->require_id, $vote->created_at, $vote->updated_at, $vote->count);
           
            return new VoteResource($vote);
                
            } catch (ClientException $e) {
                // You can log the error for debugging purposes
                //TODO 이것이 왜 안되는지 확인 필요 
                return response()->json(['success' => false, 'message' => 'Failed', 'token' => null]);
            }

        //  user은 option_id 
        // 해당 option_id가 vote_id에 있는지 확인
        // 1. vote 썸네일, 제목 볼러우기
        // 2. vote 🗳️ 투표 옵션 (이미지, 선택 radio btn) 추가
        // 3. 투표 선택된 옵션 데이터 관리자로 보내기
    }

    //voting function
    //input must include "choice_id"
    public function voter(VoterRequest $request, Vote $vote)
    {

        $validated = $request->validated();
        $choice = Choice::findorFail($validated['choice_id']);

        abort_if($choice->vote_id != $vote->id, 403, "vote doesn't have the choice");

        $validated['vote_title'] = $vote->title;
        $validated['choice_name'] = $choice->name;
        $validated['vote_id'] = $vote->id;
        $voter_id = ['user_id'=>auth()->id()];

        //$voter = $choice->voters()->updateOrCreate($voter_id, $validated);
        
        //이미 투표하였는지 확인
        $voter = Voter::where('user_id', Auth::user()->id)->where('choice_id', $choice->id)->first();
        if($voter) {
            return response()->json([ 'message' => '이미 투표하였습니다.']);
        } else {
            $voter = $choice->voters()->updateOrCreate($voter_id, $validated);
            return response()->json(['message' => '투표가 완료되었습니다', 'data' => $voter]);
        }
        
    }
}
