<?php

namespace App\Http\Controllers\Admin\Vote;

use App\Models\Vote\Vote;
use App\Models\Vote\Voter;
use App\Models\Vote\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\Vote\VoterRequest;
use App\Http\Resources\Admin\Vote\VoteResource;
use App\Http\Resources\Admin\Vote\VoterResource;
use App\Http\Resources\Admin\Vote\VoteCollection;
use App\Http\Resources\Admin\Vote\VoterCollection;
use App\Http\Requests\Admin\Vote\StoreVoteRequest;
use App\Http\Requests\Admin\Vote\UpdateVoteRequest;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Carbon\Carbon;

class VoteController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->itemsPerPage ? $request->itemsPerPage : 10;

        $votes = QueryBuilder::for(Vote::class)
                ->allowedFilters([
                    AllowedFilter::callback('search', function($query, $value){
                        $query->where('title', 'LIKE', '%' . $value . '%')
                        ->orWhere('name', 'LIKE', '%' . $value . '%');
                    }),
                    AllowedFilter::callback('startdate', function ($query, $value) {
                        return $query->where('start_at', '>=', $value);
                    }),
                    AllowedFilter::callback('enddate', function ($query, $value) {
                        return $query->where('end_at', '<=', Carbon::parse($value)->addDay());
                    })
                ])
            ->defaultSort('created_at')
            ->paginate($itemsPerPage);

        return new VoteCollection($votes);
    }

    public function show(Request $request, Vote $vote)
    {
        return new VoteResource($vote);
    }


    //voting function
    //input must include "choice_id"
    public function voter(VoterRequest $request, Vote $vote)
    {
        $validated_data = $request->validated();

        $choice = Choice::findorFail($validated_data['choice_id']);

        abort_if($choice->vote_id != $vote->id, 403, "vote doesn't have the choice");

        $validated_data['vote_title'] = $vote->title;

        $voter_id = ['user_id'=>auth()->id()];

        $vote->voters()->updateOrCreate($voter_id, $validated_data);

        return new VoteResource($vote);
    }


    public function store(StoreVoteRequest $request)
    {

        $validated = $request->validated();

        $vote = Auth::user()->votes()->create($validated);

        //insert image
        if($request->hasFile('image')) {
        $vote->addMediaFromRequest('image')
            ->toMediaCollection('vote_image', 's3');//(collection name column, filesystems.php::disk)
        }

        return new VoteResource($vote);
    }

    public function update(UpdateVoteRequest $request, Vote $vote)
    {
        $validated = $request->validated();

        //abort_if(Auth::user()->isnot($vote->user), 400, 'User is not authorized to update the vote project.');

        $vote->update($validated);

        //update image
        if($request->hasFile('image')) {
            $vote->clearMediaCollection('vote_image');
            $vote->addMediaFromRequest('image')
                ->toMediaCollection('vote_image', 's3');//(collection_name column, filesystems.php::disk)
            }

        return new VoteResource($vote);

    }

    public function destroy(Request $request, Vote $vote)
    {
        //abort_if(Auth::user()->isnot($vote->user), 400, 'User is not authorized to delete the vote project.');

        $vote->delete();

        return new VoteCollection(Vote::all());
    }

    public function voterList(Request $request, $id)
    {

        $itemsPerPage = $request->itemsPerPage ? $request->itemsPerPage : 10;

        $votes = QueryBuilder::for(Voter::class)
                ->leftJoin('choices', 'voters.choice_id', '=', 'choices.id')
                ->leftJoin('users', 'voters.user_id', '=', 'users.id')
                ->allowedFilters([
                    AllowedFilter::callback('search', function($query, $value){
                        $query->where('vote_title', 'LIKE', '%' . $value . '%')
                        ->orWhere('choice_name', 'LIKE', '%' . $value . '%');
                    }),
                    AllowedFilter::callback('startdate', function ($query, $value) {
                        return $query->where('start_at', '>=', $value);
                    }),
                    AllowedFilter::callback('enddate', function ($query, $value) {
                        return $query->where('end_at', '<=', Carbon::parse($value)->addDay());
                    })
                ]);

            if($id) { $votes = $votes->where('choices.vote_id',$id); }
            $votes = $votes->defaultSort('-voters.created_at')
            ->paginate($itemsPerPage);
        
        return new VoterCollection($votes);
    }

}
