<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Answer;
use App\Models\UserQuizState;

// モデルは単数系
class User extends Model
{
    // Flightモデルがflightsテーブルにレコードを格納し、AirTrafficControllerモデルはair_traffic_controllersテーブルにレコードを格納
    /**
     * モデルに関連付けるテーブル
     *
     * @var string
     */
    // protected $table = 'users';

    /**
     * テーブルに関連付ける主キー
     *
     * @var string
     */
    // protected $primaryKey = 'flight_id';

    // タイムスタンプの保存に使用するカラム名をカスタマイズ
    const CREATED_AT = "ins_timestamp";
    const UPDATED_AT = "upd_timestamp";

    protected $fillable = [
        "last_name",
        "first_name",
        "last_name_kana",
        "first_name_kana",
        "email",
        "phone_number",
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function userQuizStates()
    {
        return $this->hasMany(UserQuizState::class);
    }
}
