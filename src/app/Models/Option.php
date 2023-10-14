<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    // タイムスタンプの保存に使用するカラム名をカスタマイズ
    const CREATED_AT = "ins_timestamp";
    const UPDATED_AT = "upd_timestamp";

    // protected $fillable = [
    //     'user_id',
    //     'current_question_id',
    //     'question_phase',
    // ];
}
