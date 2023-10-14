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
                ->integer("manager_id")
                ->unsigned()
                ->nullable();
            $table
                ->integer("user_id")
                ->unsigned()
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("line_info", function (Blueprint $table) {
            $table->dropColumn("manager_id");
            $table->dropColumn("user_id");
        });
    }
};
