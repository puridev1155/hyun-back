<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        //Role 과 Permission 이라는 Eloquent Model 이 제공되므로 이걸로 role 과 permission 을 만들 수 있습니다.
        //role 은 permission 의  그룹이므로 개별 permission 은 2가지 방법으로 role 과 연결할 수 있습니다.
        //먼저 Role 클래스의 givePermissionTo(Permission $permission) 을 사용할 수 있습니다.

        //create permissions for post
        Permission::create(['name' => 'post.store']);
        Permission::create(['name' => 'post.update']);
        Permission::create(['name' => 'post.destroy']);

        //create roles available;
        Role::create(['name' => 'admin'])->givePermissionTo(Permission::all());

        Role::create(['name' => 'manager'])->givePermissionTo([
            'post.store',
            'post.update',
            'post.destroy',
        ]);
    }
}
