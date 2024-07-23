<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'name' => 'Văn Bách',
            'email' => 'youngbach@gmail.com',
            'password' => Hash::make('admin'),
            'role_id' => 1
        ]);

        User::create([
            'name' => 'Huy Nguyễn',
            'email' => 'huynguyen@gmail.com',
            'password' => Hash::make('admin'),
            'role_id' => 2

        ]);

        User::create([
            'name' => 'Thế Hải',
            'email' => 'thehai@gmail.com',
            'password' => Hash::make('admin'),
            'role_id' => 3
        ]);
    }
}
