<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SupportRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Support;

class SupportController extends Controller
{
    public function supportStore(SupportRequest $request) {

        $validated = $request->validated();
        $validated['category_id'] = 9;
        $validated['user_id'] = Auth::user()->id;
        $support = Support::create($validated);
        return response()->json($support);

    }

    public function promotionStore(SupportRequest $request) {
     
        $validated = $request->validated();
        $validated['category_id'] = 10;
        $validated['user_id'] = Auth::user()->id;
        $support = Support::create($validated);
        return response()->json($support);
    }
}
