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
        Schema::table("line_info", function (Blueprint $table) {
            $table
                ->integer("sync_step_cd")
                ->default(0)
                ->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("line_info", function (Blueprint $table) {
            $table->dropColumn("sync_step_cd");
        });
    }
};
