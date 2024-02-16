<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('users')->insert([
      "name" => "admin",
      "email" => "admin@correo.com",
      "password" => Hash::make("\$up3RU\$4r1o")
    ]);

    $user = User::find(1);
    $user->roles()->attach(1);
  }
}
