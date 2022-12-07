<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'company_craete',
            'company_edit',
            'company_delete',
            'user_edit',
            'user_delete',
            'user_access',
            'product_create',
            'product_update',
            'product_delete',
            'coin_edit',
            'group_create',
            'group_edit',
            'group_delete',
            'transaction_access'
        ];

        foreach ($permissions as $permission) {
            Permission::query()->create([
                'name' => $permission
            ]);
        }
    }
}
