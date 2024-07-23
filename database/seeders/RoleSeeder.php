<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Role::create([
            'name' => 'admin',
            'description' => 'Quản trị viên',
        ]);

        Role::create([
            'name' => 'employee',
            'description' => 'Nhân viên',
        ]);

        Role::create([
            'name' => 'sale',
            'description' => 'Nhân viên bán hàng',
        ]);
    }
}
