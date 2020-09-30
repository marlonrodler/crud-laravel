<?php

use App\Models\User;
use Illuminate\Database\Seeder;
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

        factory(User::class)->create([
            'email' => 'admin@dev.com',
            'password' => Hash::Make('123'),
            'status' => User::STATUS_ACTIVE,
        ]);

        factory(User::class, 5)->create([
            'password' => Hash::Make('123'),
        ]);
    }
}
