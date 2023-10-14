<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Question;

class Answer extends Model
{
    // タイムスタンプの保存に使用するカラム名をカスタマイズ
    const CREATED_AT = "ins_timestamp";
    const UPDATED_AT = "upd_timestamp";

    protected $fillable = ["user_id", "question_id", "option_id"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
