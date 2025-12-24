<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;



class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users=User::factory()->count(10)->create();
        foreach ($users as $user) {
            $token=JWTAuth::fromUser($user);
        }
        User::create([
            'country_code'=>'+963',
            'phone_number'=>'0960657740',
            'role'=>'owner',
            'status'=>'approved'

        ]);
        //      User::create([
        //     'country_code'=>'+963',
        //     'phone_number'=>'0960657740',
        //     'role'=>'tenant',
        //     'status'=>'approved'

        // ]);
    }

}
