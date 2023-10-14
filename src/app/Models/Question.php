<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Answer;
use App\Models\UserQuizState;
use App\Models\Option;

class Question extends Model
{
    // タイムスタンプの保存に使用するカラム名をカスタマイズ
    const CREATED_AT = "ins_timestamp";
    const UPDATED_AT = "upd_timestamp";

    protected $fillable = ["manager_id", "sort_num"];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function userQuizStates()
    {
        return $this->hasMany(UserQuizState::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
