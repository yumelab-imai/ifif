<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Manager extends Authenticatable
{
    // タイムスタンプの保存に使用するカラム名をカスタマイズ
    const CREATED_AT = "ins_timestamp";
    const UPDATED_AT = "upd_timestamp";

    protected $fillable = [
        "last_name",
        "first_name",
        "last_name_kana",
        "first_name_kana",
        "channel_id",
        "channel_secret",
        "channel_token",
    ];
}
