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
        Schema::create('cookie_records', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->longText('content');
            $table->boolean("is_active")->nullable();
            $table->json("profiles")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cookie_records');
    }
};
