<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineInfo extends Model
{
    // テーブル名が『line_infos』テーブルとして扱われるのを防ぐ
    protected $table = "line_info";

    // 複数代入の脆弱性に対応
    protected $fillable = [
        "line_id",
        "displayName",
        "language",
        "pictureUrl",
        "statusMessage",
        "temp_email",
        "sync_step_cd",
        "manager_id",
        // このように記入しないと、データとして代入されていてもnullとして代入されてしまう。
        "user_id",
    ];

    // タイムスタンプの保存に使用するカラム名をカスタマイズ
    const CREATED_AT = "ins_timestamp";
    const UPDATED_AT = "upd_timestamp";
}
