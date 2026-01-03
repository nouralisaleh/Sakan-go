<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Admin::updateOrCreate(
    ['email' => 'admin@gmail.com'],
    [
        'password' => Hash::make('Admin123'),
        'name'=>'admin panel',
        'birth_date' => '1995-05-10',
        'personal_image' => 'storage\app\private\Admin\personal_images\1\Shanghai Tower (3).jpg',
        'id_image' => 'storage\app\private\Admin\id_images\1\Shanghai Tower (3).jpg',
        'phone_number' => '0981915237',
        'country_code' => '+963',
    ]
);
    }
}
