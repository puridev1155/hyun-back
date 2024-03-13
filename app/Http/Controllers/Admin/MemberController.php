<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Payment;
use App\Models\Vote\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Resources\MemberResource;
use App\Http\Resources\Admin\MemberCollection;
use App\Http\Requests\Admin\StoreMemberRequest;
use App\Http\Requests\Admin\UpdateMemberRequest;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

//메인 모델: User model
//콘트롤러collection: 다보여주는거
//콘트롤러resource: 하나만 보여줌 대신 (foreign key에 연결된 모델까지 다 보여줌)

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->itemsPerPage ? $request->itemsPerPage : 10;


        if (Cache::has('users')) {

            $users = Cache::get('users');

        } else {
            // filter (in url): ?filter[key]=value&filter[key]=value...
            // sort (in url): ?sort=key, key... (decending if -key)
            // filter[search] = name/email명으로 검색.
            // startDate, Enddate parameter 추가
            $users = QueryBuilder::for(User::class)
                ->allowedFilters([
                    AllowedFilter::callback('search', function($query, $value){
                        $query->where('name', 'LIKE', '%' . $value . '%')
                        ->orWhere('email', 'LIKE', '%' . $value . '%');
                    }),
                    AllowedFilter::callback('startdate', function ($query, $value) {
                        return $query->where('updated_at', '>=', $value);
                    }),
                    AllowedFilter::callback('enddate', function ($query, $value) {
                        return $query->where('updated_at', '<=', Carbon::parse($value)->addDay());
                    }),
                    'name',
                    'birth',
                    'gender',
                    'email',
                    'country_id',
                    'membership',
                    'lang_id',
                    'role',
                ])
            ->whereNull('deleted_at')
            ->defaultSort('-created_at')
            ->allowedSorts(['birth', 'nickname', 'membership', 'role'])
            ->paginate($itemsPerPage);

            Cache::put('users', $users, 1);
        }

        return new MemberCollection($users);
    }

    public function show(Request $request, User $member)
    {
        $profile = $member->getMedia('profile_image')->last();
        if($profile) {
            $image = $profile->original_url ? $profile->original_url : null;
        } else {
            $image = null;
        }
        $member['profile_url'] = $image;
        Log::debug(['member' => $member->id ]);
        return new MemberResource($member);
    }

    public function history(Request $request, User $member) {
        $data['payment'] = Payment::where('user_id', $member->id)->get();
        $data['voter'] = Voter::where('user_id', $member->id)->get();
        return response()->json($data);
    }

    public function store(StoreMemberRequest $request)
    {
        $validated = $request->validated();
        $user = User::create($validated);

        return new MemberResource($user);
    }

    public function update(UpdateMemberRequest $request, User $member)
    {

        $validated = $request->validated();
        if($request->password) { //새 비밀번호 있을 경우 HASH 처리
            $validated['password'] = Hash::make($request->password);
        }
        $member->update($validated);

        return new MemberResource($member);
    }

    public function destroy(Request $request, User $member) //user 삭제
    {
        $member->delete();

        return response()->noContent();
    }

    public function showTrash(Request $request)
    {
        $users = User::onlyTrashed()->paginate();

        return response()->json($users);
    }

    public function restoreTrash(Request $request, int $id)
    {
        $member = User::withTrashed()->find($id);

        $member->restore();

        return new MemberCollection($member);
    }

    public function destroyTrash(Request $request, int $id)
    {
        $member = User::withTrashed()->find($id);

        $member->forceDelete();

        return response()->noContent();
    }
}
