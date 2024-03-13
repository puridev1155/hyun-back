<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    const MENUS = [
        'member' => '회원 관리',
        'audition' => '오디션 관리',
        'ticket' => '티켓 관리',
        'participant' => '참여자 관리',
        'vote' => '투표 관리',
        'voter' => '투표자 관리',
        'setting' => '설정',
    ];

    const SUB_MENUS = [
        //회원관리 탭
        'members' => ['title' => '전체 회원 현황', 'url' => '/members'],
        'trashed_members' => ['title' => '탈퇴 회원 현황', 'url' => '/trash/members'],

        // 'import_members' => ['title' => '엑셀 일괄 등록', 'to' => 'members/import'],
        // 'referrer' => ['title' => '추천인 현황', 'to' => 'members/referrer'],
        // 'blacklist' => ['title' => '블랙리스트 현황', 'to' => 'members/blacklist'],
        // 'qnas' => ['title' => '고객센터', 'to' => 'qnas'],


        //오디션 탭
        'posts' => ['title' => '전체 오디션 현황', 'url' => '/posts'],

        //티켓 탭
        'posts' => ['title' => '전체 티켓 현황', 'url' => '/posts'],

        //참여자 관리
        'participants' => ['title' => '전체 참여자 현황', 'url' => '/participants'],

        //투표 관리
        'vote' => ['title' => '전체 투표 현황', 'url' => '/votes'],
    ];

    public static function getMenus()
    {
        $results = [];

        $admin = User::findOrFail(Auth::user()->id);
        $permissions = $admin->getAllPermissions()->filter(fn($e) => Str::endsWith($e->name, 'index'));
        foreach ($permissions as $p) {
            list($menu, $subMenu, $action) = explode('.', $p->name);
            if (!isset(self::MENUS[$menu])) continue;
            $results[$menu]['title'] = self::MENUS[$menu];
            if (isset(self::SUB_MENUS[$subMenu])) {
                $results[$menu]['menus'][] = self::SUB_MENUS[$subMenu];
            }
        }

        $menus = [];
        foreach ($results as $k => $value) {
            $menus[] = ['title' => $value['title'], 'sub_menus' => $value['menus']];
        }

        return $menus;
    }

    public static function setPermissions()
    {
        // //clear the previous permissions and roles
        DB::table('role_has_permissions')->delete();
        DB::table('model_has_permissions')->delete();
        DB::table('model_has_roles')->delete();
        DB::table('roles')->delete();
        DB::table('permissions')->delete();

        //set roles
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $manager = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $super_manager = Role::create(['name' => 'manager', 'guard_name' => 'web']);

        //set permissions

            //대메뉴|소메뉴|기능
            /*member.members.index
            member.members.create
            member.members.store
            member.members.show
            member.members.edit
            member.members.update
            member.members.destroy*/

        $admin->givePermissionTo(
            //회원 관리
            Permission::create(['name' => 'member.members.index', 'guard_name' => 'web']),
            Permission::create(['name' => 'member.members.show', 'guard_name' => 'web']),
            Permission::create(['name' => 'member.members.store', 'guard_name' => 'web']),
            Permission::create(['name' => 'member.members.update', 'guard_name' => 'web']),
            Permission::create(['name' => 'member.members.destroy', 'guard_name' => 'web']),

            Permission::create(['name' => 'member.trashed_members.index', 'guard_name' => 'web']),
            Permission::create(['name' => 'member.trashed_members.restore', 'guard_name' => 'web']),
            Permission::create(['name' => 'member.trashed_members.destroy', 'guard_name' => 'web']),

            //오디션 관리
            Permission::create(['name' => 'audition.posts.index', 'guard_name' => 'web']),
            Permission::create(['name' => 'audition.posts.store', 'guard_name' => 'web']),
            Permission::create(['name' => 'audition.posts.update', 'guard_name' => 'web']),
            Permission::create(['name' => 'audition.posts.delete', 'guard_name' => 'web']),

            //티켓 관리
            Permission::create(['name' => 'ticket.posts.index', 'guard_name' => 'web']),
            Permission::create(['name' => 'ticket.posts.store', 'guard_name' => 'web']),
            Permission::create(['name' => 'ticket.posts.update', 'guard_name' => 'web']),
            Permission::create(['name' => 'ticket.posts.delete', 'guard_name' => 'web']),

            //참여자 관리
            Permission::create(['name' => 'participant.participants.index', 'guard_name' => 'web']),
            Permission::create(['name' => 'participant.participants.store', 'guard_name' => 'web']),
            Permission::create(['name' => 'participant.participants.update', 'guard_name' => 'web']),
            Permission::create(['name' => 'participant.participants.delete', 'guard_name' => 'web']),

            //투표 관리
            Permission::create(['name' => 'vote.votes.index', 'guard_name' => 'web']),
            Permission::create(['name' => 'vote.votes.store', 'guard_name' => 'web']),
            Permission::create(['name' => 'vote.votes.update', 'guard_name' => 'web']),
            Permission::create(['name' => 'vote.votes.delete', 'guard_name' => 'web']),

            //투표 선택 관리
            Permission::create(['name' => 'vote.choices.index', 'guard_name' => 'web']),
            Permission::create(['name' => 'vote.choices.store', 'guard_name' => 'web']),
            Permission::create(['name' => 'vote.choices.update', 'guard_name' => 'web']),
            Permission::create(['name' => 'vote.choices.delete', 'guard_name' => 'web']),
        );

        $manager->givePermissionTo(
             //회원 관리
             Permission::create(['name' => 'member.members.index', 'guard_name' => 'web']),
             Permission::create(['name' => 'member.members.show', 'guard_name' => 'web']),

        );

        // $super_admin->getAllPermissions();

        //initial setting but this can change!!
        $user = User::find(1);
        $user->assignRole($admin);

    }
}
