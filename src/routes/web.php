<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ManagerController;
use App\Http\Controllers\Manager\LineInfoController;
use App\Http\Controllers\Manager\QuestionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get("/", function () {
    return view("welcome");
});

Route::group(["middleware" => ["auth:manager"]], function () {
    Route::get("/manager", [ManagerController::class, "top"])->name(
        "manager.top"
    );
    Route::get("/manager/analysis", function () {
        return view("manager.analysis");
    })->name("manager.analysis");
    Route::get("/manager/line-info", [
        LineInfoController::class,
        "index",
    ])->name("manager.chat");
    Route::get("/manager/chat", [LineInfoController::class, "chat"])->name(
        "manager.line_info"
    );
    // 質問関連
    Route::get("/manager/create-question", [
        QuestionController::class,
        "create",
    ])->name("manager.create_question");
    Route::post("/manager/create-question", [
        QuestionController::class,
        "store",
    ])->name("manager.store_question");
    Route::get("/manager/questions/{id}/edit", [
        QuestionController::class,
        "edit",
    ])->name("manager.edit_question");
    Route::put("/manager/questions/{id}", [
        QuestionController::class,
        "update",
    ])->name("manager.update_question");
    Route::get("/manager/question-list", [
        QuestionController::class,
        "index",
    ])->name("manager.question_list");
    Route::post("manager/logout", [ManagerController::class, "logout"])->name(
        "manager.logout"
    );
    Route::get("/manager/settings", [
        ManagerController::class,
        "settings",
    ])->name("manager.settings");
    Route::post("/manager/settings/update", [
        ManagerController::class,
        "updateSettings",
    ])->name("manager.settings.update");
});

Route::get("/manager/register", [
    ManagerController::class,
    "showRegisterForm",
])->name("manager.register.page");
Route::post("/manager/register", [ManagerController::class, "register"])->name(
    "manager.register"
);
Route::get("/manager/login", [ManagerController::class, "showLoginForm"])->name(
    "manager.login.page"
);
Route::post("/manager/login", [ManagerController::class, "login"])->name(
    "manager.login"
);

Route::post(
    "/line/webhook/message",
    "App\Http\Controllers\LineWebhookController@message"
)->name("line.webhook.message");
