<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("managers", function (Blueprint $table) {
            // 長さが足りないので、1000に変更
            $table
                ->string("channel_secret", 1000)
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("managers", function (Blueprint $table) {
            $table
                ->string("channel_secret", 500)
                ->nullable()
                ->change();
        });
    }
};
