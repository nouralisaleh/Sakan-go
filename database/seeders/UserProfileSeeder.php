<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserProfile::create([
            'user_id' => 1,
            'first_name' => 'Owner',
            'last_name' => 'One',
            'birth_date' => '1990-01-01',
            'personal_image' => 'Users/personal_images/1/image.jpg',
            'id_image' => 'Users/id_images/1/id.jpg',
        ]);

        UserProfile::create([
            'user_id' => 2,
            'first_name' => 'nourya',
            'last_name' => 'One',
            'birth_date' => '1990-01-01',
            'personal_image' => 'Users/personal_images/2/image.jpg',
            'id_image' => 'Users/id_images/2/id.jpg',
        ]);
    }
}