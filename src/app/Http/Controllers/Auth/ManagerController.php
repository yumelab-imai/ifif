<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Manager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class ManagerController extends Controller
{
    /**
     * ログイン処理
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $user_info = $request->only("email", "password"); // @scrf を除外

        // attemptメソッドで、データベースにある該当ユーザーを探す。パスワードは生のままでよく、ハッシュ化させなくて大丈夫
        // 暗号化方式: bcrypt
        if (Auth::guard("manager")->attempt($user_info)) {
            // true or false
            // ログイン成功
            $request->session()->regenerate(); // セッション固定攻撃（Session Fixation Attack）対策
            return redirect()->route("manager.top");
        } else {
            // ログイン失敗
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    "login_check" => "該当するユーザーが存在しません。",
                ]);
        }
    }

    /**
     * トップページ
     */
    public function top()
    {
        $channel_id = Auth::guard("manager")->user()->channel_id;
        $channel_secret = Auth::guard("manager")->user()->channel_secret;
        $channel_token = Auth::guard("manager")->user()->channel_token;

        $channel_id = $channel_id ? Crypt::decryptString($channel_id) : "";
        $channel_secret = $channel_secret
            ? Crypt::decryptString($channel_secret)
            : "";
        $channel_token = $channel_token
            ? Crypt::decryptString($channel_token)
            : "";

        $encrypted_manager_id = Crypt::encryptString(
            Auth::guard("manager")->user()->id
        );

        $webhookURL =
            config("app.DOMAIN_URL") .
            "/line/webhook/message?manager_id=" .
            $encrypted_manager_id;
        return view("manager_top", [
            "decryptedChannel_id" => $channel_id,
            "decryptedChannel_secret" => $channel_secret,
            "decryptedChannel_token" => $channel_token,
            "webhookURL" => $webhookURL,
        ]);
    }

    /**
     * 新規登録フォーム
     */
    public function showRegisterForm()
    {
        return view("manager.auth.register");
    }
    /**
     * ログインフォーム
     */
    public function showLoginForm()
    {
        return view("manager.auth.login");
    }

    /**
     * 新規登録処理
     */
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            $manager = new Manager();

            // 登録するデータ一覧
            $manager->email = $request->email;
            $manager->password = Hash::make($request->password); // パスワードをハッシュ化
            $manager->ins_action = "登録画面からの登録";
            $manager->upd_action = "登録画面からの登録";
            $manager->email = $request->email;
            $manager->email = $request->email;
            $manager->email = $request->email;
            $manager->email = $request->email;

            DB::commit();
            $manager->save();

            return redirect()->route("manager.login.page");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            Log::error(DB::getQueryLog());
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(["error" => "登録に失敗しました。"]);
        }
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::guard("manager")->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("manager.login.page");
    }

    /**
     * アカウント設定画面
     */
    public function settings()
    {
        $decryptedChannel_token = "";
        $decryptedChannel_secret = "";
        $decryptedChannel_id = "";
        $manager = Auth::guard("manager")->user();

        if (!empty($manager->channel_token)) {
            $decryptedChannel_token = Crypt::decryptString(
                $manager->channel_token
            );
        }

        if (!empty($manager->channel_secret)) {
            $decryptedChannel_secret = Crypt::decryptString(
                $manager->channel_secret
            );
        }

        if (!empty($manager->channel_id)) {
            $decryptedChannel_id = Crypt::decryptString($manager->channel_id);
        }

        return view("manager.settings")->with([
            "manager" => $manager,
            "decryptedChannel_token" => $decryptedChannel_token,
            "decryptedChannel_secret" => $decryptedChannel_secret,
            "decryptedChannel_id" => $decryptedChannel_id,
        ]);
    }

    /**
     * アカウント設定更新処理
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            "last_name" => "string|nullable|max:50",
            "first_name" => "string|nullable|max:50",
            "last_name_kana" => "string|nullable|max:50",
            "first_name_kana" => "string|nullable|max:50",
            "channel_token" => "string|nullable|max:250",
            "channel_secret" => "string|nullable|max:250",
            "channel_id" => "string|nullable|max:250",
        ]);

        try {
            DB::beginTransaction();
            $manager = Auth::guard("manager")->user();

            $manager->last_name = $request->last_name;
            $manager->first_name = $request->first_name;
            $manager->last_name_kana = $request->last_name_kana;
            $manager->first_name_kana = $request->first_name_kana;

            if (!empty($request->channel_token)) {
                $manager->channel_token = Crypt::encryptString(
                    $request->channel_token
                );
            } else {
                $manager->channel_token = null;
            }

            if (!empty($request->channel_secret)) {
                $manager->channel_secret = Crypt::encryptString(
                    $request->channel_secret
                );
            } else {
                $manager->channel_secret = null;
            }

            if (!empty($request->channel_id)) {
                $manager->channel_id = Crypt::encryptString(
                    $request->channel_id
                );
            } else {
                $manager->channel_id = null;
            }

            $manager->save();

            DB::commit();
            return redirect()
                ->route("manager.settings")
                ->with("success", "Settings updated successfully");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->route("manager.settings")
                ->withErrors(["error" => "Updating settings failed."]);
        }
    }
}
