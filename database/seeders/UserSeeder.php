<?php

namespace Database\Seeders;

use App\Models\User;
use DB;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resources = [
            'Product',
            'Purchasing Transaction',
            'Selling Transaction',
            'User',
            'Role',
            'Permission',
        ];

        $actions = ['view-any', 'view', 'create', 'update', 'delete'];

        $permissions = [];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "$action $resource",
                    'guard_name' => 'web',
                ]);
            }
        }

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $role->syncPermissions(Permission::all());

        $user = User::firstOrCreate(
            ['email' => 'superadmin@mm162.com'],
            [
                'name' => 'Superadmin Mini Market 162',
                'password' => Hash::make('SuperadminMM162'),
            ]
        );

        $user->assignRole($role);
    }
}
