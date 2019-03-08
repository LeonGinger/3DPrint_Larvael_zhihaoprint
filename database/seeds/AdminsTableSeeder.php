<?php

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = [
            'name' => 'stone',
            'password' => bcrypt('123456'),
            'last_login_at' => now()->toDateTimeString(),
        ];
        Admin::create($admin);
    }
}
