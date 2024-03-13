<?php

namespace App\Http\Controllers\Admin\Setting;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\MemberResource;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Resources\Admin\MemberCollection;
use App\Http\Requests\Admin\UpdateAdminRequest;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->itemsPerPage ? $request->itemsPerPage : 10;

        $adminRole = Role::where('name', 'admin')->first();
        $managerRole = Role::where('name', 'manager')->first();


        if (Cache::has('admins')) {

            $users = Cache::get('admins');

        } else {
            // filter (in url): ?filter[key]=value&filter[key]=value...
            // sort (in url): ?sort=key, key... (decending if -key)
            // filter[search] = name/email명으로 검색.
            // startDate, Enddate parameter 추가
            $users = QueryBuilder::for(User::role([$adminRole, $managerRole]))
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
                    'lang',
                    'role',
                ])
            ->whereNull('deleted_at')
            ->defaultSort('-created_at')
            ->allowedSorts(['birth', 'nickname', 'membership', 'role'])
            ->paginate($itemsPerPage);

            Cache::put('admins', $users, 1);
        }

        return AdminResource::collection($users);;
    }



    public function show(Request $request, User $admin)
    {
        $admin->getPermissionsViaRoles();

        return $admin['roles'][0];
    }

    public function update(UpdateAdminRequest $request, User $admin)
    {
        //"roles": "admin" or "manager".

        $admin->assignRole($request['roles']);

        $admin->getPermissionsViaRoles();

        return new AdminResource($admin);

    }

    public function destroy(Request $request, User $admin) //user 삭제
    {
        $admin->removeRole('admin');
        $admin->removeRole('manager');

        return $admin;
    }
}
