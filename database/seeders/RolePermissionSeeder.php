<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission = [
            'manage user details',
            'manage companies',
            'manage categories',
            'manage standard operationals',
            'manage policy letters',
            'manage internal memos',
            'manage meeting memos',
            'manage audit trails',
        ];

        foreach($permission as $permission){
            Permission::firstOrCreate(
                [
                    'name' => $permission
                ]
            );
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'admin'
        ]);

        $adminPermissions = [
            'manage user details',
            'manage companies',
            'manage categories',
            'manage standard operationals',
            'manage policy letters',
            'manage internal memos',
            'manage meeting memos',
            'manage audit trails',
        ];

        $adminRole->syncPermissions($adminPermissions);

        $viewerRole = Role::firstOrCreate([
            'name' => 'viewer'
        ]);

        $viewerPermissions = [
            // 'manage user details',
            // 'manage companies',
            // 'manage categories',
            // 'manage standard operationals',
            // 'manage policy letters',
            // 'manage audit trails',
        ];

        $viewerRole->syncPermissions($viewerPermissions);

        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin'
        ]);

        $superAdminPermissions = [
            'manage user details',
            'manage companies',
            'manage categories',
            'manage standard operationals',
            'manage policy letters',
            'manage internal memos',
            'manage meeting memos',
            'manage audit trails',
        ];

        $superAdminRole->syncPermissions($superAdminPermissions);

        $user = User::firstOrCreate(
            ['email' => 'admin@papandayan.co.id'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('papandayan@2026'),
            ]
        );

        $user->syncRoles([$superAdminRole]);
    }
}
