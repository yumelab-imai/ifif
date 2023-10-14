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
        if (!Schema::hasTable("managers")) {
            Schema::create("managers", function (Blueprint $table) {
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
                $table->string("last_name", 50)->nullable();
                $table->string("first_name", 50)->nullable();
                $table->string("last_name_kana", 50)->nullable();
                $table->string("first_name_kana", 50)->nullable();
                $table
                    ->string("email", 50)
                    ->unique()
                    ->nullable();
                // 「-（ハイフン）」が入ってる形式、海外からの電話番号( 01など）多くの形式があるので、とりあえずvarchar型で定義して、バリデーションで質を担保
                $table
                    ->string("phone_number", 16)
                    ->unique()
                    ->nullable(); // 電話番号の列を追加（最大16桁）
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("managers");
    }
};
