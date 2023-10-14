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
        if (!Schema::hasTable("line_info")) {
            Schema::create("line_info", function (Blueprint $table) {
                $table->increments("id");
                $table->timestamp("ins_timestamp")->useCurrent();
                $table->integer("ins_user_id")->default(0);
                $table->string("ins_action")->nullable();
                $table->timestamp("upd_timestamp")->nullable();
                $table->integer("upd_user_id")->default(0);
                $table->string("upd_action")->nullable();
                $table->timestamp("del_timestamp")->nullable();
                $table->integer("del_user_id")->default(0);
                $table->string("del_action")->nullable();
                $table->boolean("del_flag")->default(0);
                // 「親」テーブルのレコードが削除された場合に、「子」テーブルのレコードも自動的に削除
                $table
                    ->foreign("user_id")
                    ->references("id")
                    ->on("managers")
                    ->onDelete("cascade");
                $table->string("line_id");
                $table->string("displayName");
                $table->string("language")->nullable();
                $table->string("pictureUrl")->nullable();
                $table->string("statusMessage")->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("line_info");
    }
};
