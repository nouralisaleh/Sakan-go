<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;



class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            'country_code'=>'+963',
            'phone_number'=>'0960657740',
            'role'=>'owner',
            'status'=>'approved'

        ]);
             User::create([
            'country_code'=>'+963',
            'phone_number'=>'0959643053',
            'role'=>'tenant',
            'status'=>'approved'

        ]);


    }

}
