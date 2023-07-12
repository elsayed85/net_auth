<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $sql = storage_path("app/data.sql");
        DB::unprepared(file_get_contents($sql));

        User::create([
            "name" => "Sayed",
            "phone" => "01092291556",
            "password" => Hash::make("password"),
        ]);
    }
}
