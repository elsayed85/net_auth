<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // m-m user cookie
        Schema::create('user_cookie', function (Blueprint $table) {
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->foreignId("cookie_id")->constrained("cookie_records")->onDelete("cascade");
            $table->primary(["user_id", "cookie_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_cookies');
    }
};
