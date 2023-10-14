<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\UserQuizState;
use App\Models\Option;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function index()
    {
        $managerId = Auth::guard("manager")->user()->id;

        $questions = Question::where("manager_id", $managerId)
            ->with("options")
            ->orderBy("sort_num", "asc")
            ->get();

        return view("manager.question_list", compact("questions"));
    }

    // 新しい質問を作成するためのビューを表示
    public function create()
    {
        return view("manager.create_question");
    }

    // DBトランザクションもしっかり入れましょう！
    public function store(Request $request)
    {
        $managerId = Auth::guard("manager")->user()->id;

        // 新しい質問を作成
        $question = new Question();
        $question->question = $request->input("question");
        $question->manager_id = $managerId;
        $question->sort_num = $request->input("sort_num");
        $question->save();

        // 選択肢を作成
        $options = explode(",", $request->input("options")); // 選択肢は配列として受け取る
        foreach ($options as $value) {
            $option = new Option();
            $option->value = $value;
            $option->question_id = $question->id;
            $option->save();
        }

        return redirect()
            ->route("manager.question_list")
            ->with("success", "新しい質問が作成されました");
    }

    public function edit($id)
    {
        $question = Question::with("options")->findOrFail($id);
        return view("manager.edit_question", compact("question"));
    }

    public function update(Request $request, $id)
    {
        $managerId = Auth::guard("manager")->user()->id;

        // 指定されたIDの質問を取得
        $question = Question::where("manager_id", $managerId)->findOrFail($id);

        // 質問を更新
        $question->question = $request->input("question");
        $question->sort_num = $request->input("sort_num");
        $question->save();

        // 既存の選択肢を削除
        $question->options()->delete();

        // 新しい選択肢を作成
        $options = explode(",", $request->input("options"));
        foreach ($options as $value) {
            $option = new Option();
            $option->value = $value;
            $option->question_id = $question->id;
            $option->save();
        }

        return redirect()
            ->route("manager.question_list")
            ->with("success", "質問が更新されました");
    }
}
