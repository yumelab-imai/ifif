<?php

namespace App\Http\Controllers\Manager;

use App\CommonConstants;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LineInfo;

class LineInfoController extends Controller
{
    public function index()
    {
        $managerId = Auth::guard("manager")->user()->id;
        $lineInfos = LineInfo::where("manager_id", $managerId)
            ->where("del_flag", CommonConstants::DEL_FLG["OFF"])
            ->get();

        return view("manager.line_info", ["lineInfos" => $lineInfos]);
    }
    public function chat()
    {
        return view("manager.line_chat");
    }
}
