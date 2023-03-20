<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::truncate();

        $user1 = User::create([
            'name' => 'Santiago Argilla',
            'email' => 'sargilla@gmail.com',
            'password' => '$2y$10$zsy6ncNm0Qwwa45A1IH/gebY.bDcGWVCX0hs3aSmdQXEjKQIJzTfO',
            'settings' => '{}',
            'activated' => 1
        ]);

        $user1->assignRole('Superadmin');
    }
}
