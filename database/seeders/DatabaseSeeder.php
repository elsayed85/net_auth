<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\CookieRecord;
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
            "unlimited" => true,
            "password" => Hash::make("password"),
        ]);

        $ahmed = User::create([
            "name" => "Ahmed",
            "phone" => "01092291557",
            "unlimited" => false,
            "password" => Hash::make("password"),
        ]);

        $ahmed->cookies()->attach(CookieRecord::take(4)->get());
    }
}
