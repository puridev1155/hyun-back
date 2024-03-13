<?php

namespace App\Http\Controllers\Admin\Vote;

use App\Models\Vote\Vote;
use App\Models\Vote\Choice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Vote\ChoiceResource;
use App\Http\Resources\Admin\Vote\ChoiceCollection;
use App\Http\Requests\Admin\Vote\StoreChoiceRequest;
use App\Http\Requests\Admin\Vote\UpdateChoiceRequest;

class ChoiceController extends Controller
{
    public function index(Request $request, Vote $vote)
    {
        $choices = Choice::where('vote_id', $vote->id)->orderBy('order', 'asc')->get();
        return new ChoiceCollection($choices);
    }

    public function show(Request $request, Vote $vote, Choice $choice) {
        $choice = Choice::where('id', $choice->id)->first();
        return new ChoiceResource($choice);
    }

    public function store(StoreChoiceRequest $request, Vote $vote)
    {
        $validated = $request->validated();
        $choice = $vote->choices()->create($validated);
        //insert image
        $choice->addMediaFromRequest('image')
            //->toMediaCollection('choice_image', 'choices');//(collection name column, filesystems.php::disk)
            ->toMediaCollection('choice_image', 's3');

        return new ChoiceResource($choice);
    }

    public function update(UpdateChoiceRequest $request, Vote $vote, Choice $choice)
    {
        $validated = $request->validated();
        //abort_if($choice -> vote_id != $vote -> id, 400, 'Vote does not have the chosen choice to update.');
        //return [$vote, $choice, $validated];
        $choice->update($validated);

        //update image
       if($request->hasFile('image')) {
            $choice->clearMediaCollection('choice_image');
            $choice->addMediaFromRequest('image')
            ->toMediaCollection('choice_image', 's3');//(collection_name column, filesystems.php::disk)
            }

        return response()->json(['success' => true]);
        //$choices = $vote->choices;
        //return new ChoiceResource($choices);
    }

    public function destroy(Request $request, Vote $vote, Choice $choice)
    {
        abort_if($choice -> vote_id != $vote -> id, 400, 'Vote does not have the chosen choice to delete.');

        $choice->delete();

        $choices = $vote->choices;

        return new ChoiceCollection($choices);
    }

    public function order(Request $request, $id) { //순서 변경 업데이트
        //$id = voter id
        $data = $request->all();
        foreach($data as $key => $pid) {
                Choice::where('id', $pid)->where('vote_id', $id)->update([
                    'order' => $key + 1,
                ]);
        }

        return response()->noContent();
    }
}
