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
            $table->string("password", 250);
            $table->string("remember_token", 250)->nullable(); // 「持続ログイン（remember me）」オプションを選択したユーザーのトークンを格納するために使用
            $table
                ->string("email")
                ->nullable(false)
                ->change(); // emailカラムをnull不可に変更
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("managers", function (Blueprint $table) {
            $table->dropColumn(["password", "remember_token"]); // passwordカラムとremember_tokenカラムを削除
            $table
                ->string("email")
                ->nullable()
                ->change(); // emailカラムをnull可に変更
        });
    }
};
