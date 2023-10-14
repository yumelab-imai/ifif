<?php

namespace App\Http\Controllers;

use App\CommonConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Clients\MessagingApi\Model\UserProfileResponse;
use Illuminate\Support\Facades\Log;

use App\Models\LineInfo;
use App\Models\Manager;
use App\Models\UserQuizState;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Configuration;
use Illuminate\Support\Facades\Crypt;

use LINE\Clients\MessagingApi\Model\TemplateMessage;
use LINE\Clients\MessagingApi\Model\ConfirmTemplate;
use LINE\Clients\MessagingApi\Model\MessageAction;
use LINE\Constants\MessageType;
use LINE\Constants\TemplateType;
use LINE\Constants\ActionType;
use LINE\Clients\MessagingApi\Model\CameraAction;
use LINE\Clients\MessagingApi\Model\CameraRollAction;
use LINE\Clients\MessagingApi\Model\CarouselColumn;
use LINE\Clients\MessagingApi\Model\CarouselTemplate;
use LINE\Clients\MessagingApi\Model\URIAction;
use LINE\Clients\MessagingApi\Model\PostbackAction;
use LINE\Clients\MessagingApi\Model\QuickReply;
use LINE\Clients\MessagingApi\Model\QuickReplyItem;
use LINE\Clients\MessagingApi\Model\LocationAction;
use LINE\Clients\MessagingApi\Model\DatetimePickerAction;
use LINE\Clients\MessagingApi\Model\Emoji;
use LINE\Clients\MessagingApi\Model\ButtonsTemplate;

class LineWebhookController extends Controller
{
    // メイン機能
    public function message(Request $request)
    {
        // Log::debug("debug ログ!");
        // $data = $request->all();
        $events = $request["events"];
        // Log::debug($events);

        // クエリ文字列から暗号化されたマネージャーIDを取得
        $encrypted_manager_id = $request->query("manager_id");

        // 暗号化されたマネージャーIDを復号化
        $manager_id = Crypt::decryptString($encrypted_manager_id);
        // var_dump($manager_id );exit;

        // マネージャーテーブルから該当のマネージャー情報を取得
        $manager = Manager::find($manager_id);

        // マネージャ情報の取得
        if ($manager) {
            Log::debug("get manager info");
            $encryptedChannel_token = $manager->channel_token;
            $channel_token = Crypt::decryptString($encryptedChannel_token);
            Log::debug($manager);
        } else {
            Log::debug("failed to get manager info");
            return "access error";
        }

        // Create the bot client instance
        $client = new \GuzzleHttp\Client();
        $config = new Configuration();
        $config->setAccessToken($channel_token);
        $messagingApi = new MessagingApiApi(client: $client, config: $config);

        foreach ($events as $event) {
            // Log::debug($event['message']['text']);
            // array (
            //     'type' => 'message',
            //     'message' =>
            //     array (
            //       'type' => 'text',
            //       'id' => '474289718118056353',
            //       'quoteToken' => '6LuDNYPxJpBUHG5pyBJj69Kt5-tj730AAoBlKIEE2UuNS1Z4JzIZ3XDOTaxZiF4MHi7wUVnRdxYGsWrFFvcFFhCvmPOswAVVjp7JyP5tHwX5x8tieNwxTx1aSlx8DxNAUXkszfab4nu8EgMDeKOXJg',
            //       'text' => 'd',
            //     ),
            //     'webhookEventId' => '01HB2QDD7V777KX0X3JHTMN3FJ',
            //     'deliveryContext' =>
            //     array (
            //       'isRedelivery' => false,
            //     ),
            //     'timestamp' => 1695530005705,
            //     'source' =>
            //     array (
            //       'type' => 'user',
            //       'userId' => 'U9da723468525780ec69d31e2839b0a62',
            //     ),
            //     'replyToken' => '475404ebd4624bbaa51ea00a9b497137',
            //     'mode' => 'active',
            //   )
            switch ($event["type"]) {
                case "message": //一般的なメッセージ(文字・イメージ・音声・位置情報・スタンプ含む)
                    if ($event["message"]["text"] === "完全削除") {
                        Log::debug("action: postback");
                        $userProfileResponse = $messagingApi->getProfile(
                            userId: $event["source"]["userId"]
                        );
                        $lineId = $userProfileResponse->getUserId();
                        Log::debug("line_id: " . $lineId);
                        // 完全削除処理
                        $lineInfo = LineInfo::where("line_id", $lineId)
                            ->where("manager_id", $manager->id)
                            ->first();
                        $lineInfo->update([
                            "sync_step_cd" => 0,
                        ]);
                        $replyToken = $event["replyToken"];
                        $message = new TextMessage([
                            "type" => "text",
                            "text" =>
                                "完全削除しました。『回答する』と入力してください。",
                        ]);
                        $messagingApi->replyMessage([
                            "replyToken" => $replyToken,
                            "messages" => [$message],
                        ]);
                        break;
                    }
                    if ($event["message"]["text"] === "foo") {
                        //
                    } else {
                        Log::debug($event["replyToken"]);
                        Log::debug($event["message"]["text"]);
                        // $execute_message = $event["message"]["text"];
                        // Log::debug($execute_message);
                        $userProfileResponse = $messagingApi->getProfile(
                            userId: $event["source"]["userId"]
                        );
                        $lineId = $userProfileResponse->getUserId();
                        Log::debug("line_id: " . $lineId);
                        $lineInfo = $this->findOrCreateLineInfo(
                            $lineId,
                            $userProfileResponse,
                            $manager
                        );
                        $replyToken = $event["replyToken"];
                        $userMessage = $event["message"]["text"];

                        // 現在の質問を取得
                        $order = $lineInfo->sync_step_cd;
                        $currentQuestion = Question::where(
                            "manager_id",
                            $lineInfo->manager_id
                        )
                            ->orderBy("sort_num", "asc")
                            ->skip($order)
                            ->first();

                        if ($currentQuestion) {
                            // ユーザーの回答が選択肢に含まれるか確認
                            $option = $currentQuestion
                                ->options()
                                ->where("value", $userMessage)
                                ->first();

                            if ($option) {
                                // 正しい選択肢を選んだ場合、sync_step_cdを更新
                                $lineInfo->sync_step_cd += 1;
                                $lineInfo->save();
                                // 次の質問を送信
                                $this->sendNextQuestion(
                                    $lineInfo,
                                    $messagingApi,
                                    $event["replyToken"]
                                );
                            } else {
                                if ($userMessage == "回答する") {
                                    $errorMessage =
                                        "順番に質問に回答してください。";
                                } else {
                                    $errorMessage =
                                        "選択肢から選んでください。";
                                }
                                $this->sendQuestionWithQuickReply(
                                    $currentQuestion,
                                    $messagingApi,
                                    $event["replyToken"],
                                    $errorMessage,
                                    $order
                                );
                            }
                        } else {
                            $message = new TextMessage([
                                "type" => "text",
                                "text" =>
                                    "質問が設定されていません。もしくは、すべての質問に回答済みです。",
                            ]);
                            $messagingApi->replyMessage([
                                "replyToken" => $replyToken,
                                "messages" => [$message],
                            ]);
                        }
                        break;

                        // イベントテスト
                        // switch ($event['message']['text']) {
                        //     // OK
                        //     case 'confirm': // 『はい』、『いいえ』のボタンが表示される
                        //         Log::debug('confirm');
                        //         $templateMessage = new TemplateMessage([
                        //             'type' => MessageType::TEMPLATE,
                        //             'altText' => 'Confirm alt text',
                        //             'template' => new ConfirmTemplate([
                        //                 'type' => TemplateType::CONFIRM,
                        //                 'text' => '確認テンプレートです。',
                        //                 'actions' => [
                        //                     new MessageAction([
                        //                         'type' => ActionType::MESSAGE,
                        //                         'label' => 'No',
                        //                         'text' => 'いいえ',
                        //                     ]),
                        //                     new MessageAction([
                        //                         'type' => ActionType::MESSAGE,
                        //                         'label' => 'Yes',
                        //                         'text' => 'はい',
                        //                     ]),
                        //                 ],
                        //             ]),
                        //         ]);
                        //         // $messagingApi->replyMessage($event['replyToken'], $templateMessage);
                        //         $message = new TextMessage(['type' => 'text','text' => 'confirm']);
                        //         $messagingApi->replyMessage([
                        //             'replyToken' => $event['replyToken'],
                        //             'messages' => [
                        //                 $templateMessage,
                        //             ],
                        //         ]);
                        //         break;
                        //     case 'buttons':
                        //         $imageUrl = 'https://pbs.twimg.com/profile_images/1610819875567734785/5kM_BxFL_400x400.jpg';
                        //         $templateMessage = new TemplateMessage([
                        //             'type' => MessageType::TEMPLATE,
                        //             'altText' => 'Button alt text',
                        //             'template' => new ButtonsTemplate([
                        //                 'type' => TemplateType::BUTTONS,
                        //                 'title' => 'My button sample',
                        //                 'text' => 'Hello my button',
                        //                 'thumbnailImageUrl' => $imageUrl,
                        //                 'actions' => [
                        //                     new URIAction([
                        //                         'type' => ActionType::URI,
                        //                         'label' => 'Go to line.me',
                        //                         'uri' => 'https://line.me',
                        //                     ]),
                        //                     new PostbackAction([
                        //                         'type' => ActionType::POSTBACK,
                        //                         'label' => 'Buy',
                        //                         'data' => 'action=buy&itemid=123',
                        //                     ]),
                        //                     new PostbackAction([
                        //                         'type' => ActionType::POSTBACK,
                        //                         'label' => 'Add to cart',
                        //                         'data' => 'action=add&itemid=123',
                        //                     ]),
                        //                     new MessageAction([
                        //                         'type' => ActionType::MESSAGE,
                        //                         'label' => 'Say message',
                        //                         'text' => 'hello hello',
                        //                     ]),
                        //                 ],
                        //             ]),
                        //         ]);
                        //         $messagingApi->replyMessage([
                        //             'replyToken' => $replyToken,
                        //             'messages' => [
                        //                 $templateMessage,
                        //             ],
                        //         ]);
                        //         break;
                        //     case 'carousel':
                        //         $imageUrl = "https://aaa/com";
                        //         $templateMessage = new TemplateMessage([
                        //             'type' => MessageType::TEMPLATE,
                        //             'altText' => 'Button alt text',
                        //             'template' => new CarouselTemplate([
                        //                 'type' => TemplateType::CAROUSEL,
                        //                 'columns' => [
                        //                     new CarouselColumn([
                        //                         'title' => 'Qiita',
                        //                         'text' => 'Qiita',
                        //                         'thumbnailImageUrl' => 'https://pbs.twimg.com/profile_images/1610819875567734785/5kM_BxFL_400x400.jpg',
                        //                         'actions' => [
                        //                             new URIAction([
                        //                                 'type' => ActionType::URI,
                        //                                 'label' => 'Go to line.me',
                        //                                 'uri' => 'https://line.me',
                        //                             ]),
                        //                             new PostbackAction([
                        //                                 'type' => ActionType::POSTBACK,
                        //                                 'label' => 'Buy',
                        //                                 'data' => 'action=buy&itemid=123',
                        //                             ]),
                        //                         ],
                        //                     ]),
                        //                     new CarouselColumn([
                        //                         'title' => 'Zenn',
                        //                         'text' => 'Zenn',
                        //                         'thumbnailImageUrl' => 'https://pbs.twimg.com/profile_images/1192775453498494977/pb8Shc8G_400x400.jpg',
                        //                         'actions' => [
                        //                             new PostbackAction([
                        //                                 'type' => ActionType::POSTBACK,
                        //                                 'label' => 'Add to cart',
                        //                                 'data' => 'action=add&itemid=123',
                        //                             ]),
                        //                             new MessageAction([
                        //                                 'type' => ActionType::MESSAGE,
                        //                                 'label' => 'Say message',
                        //                                 'text' => 'hello hello',
                        //                             ]),
                        //                         ],
                        //                     ]),
                        //                 ],
                        //             ]),
                        //         ]);
                        //         $messagingApi->replyMessage([
                        //             'replyToken' => $replyToken,
                        //             'messages' => [
                        //                 $templateMessage,
                        //             ],
                        //         ]);
                        //         break;
                        // case 'quickReply':
                        //     $quickReply = new QuickReply([
                        //         'items' => [
                        //             new QuickReplyItem([
                        //                 'type' => 'action',
                        //                 'action' => new LocationAction([
                        //                     'type' => ActionType::LOCATION,
                        //                     'label' => 'Location',
                        //                 ]),
                        //             ]),
                        //             new QuickReplyItem([
                        //                 'type' => 'action',
                        //                 'action' => new CameraAction([
                        //                     'type' => ActionType::CAMERA,
                        //                     'label' => 'Camera',
                        //                 ]),
                        //             ]),
                        //             new QuickReplyItem([
                        //                 'type' => 'action',
                        //                 'action' => new CameraRollAction([
                        //                     'type' => ActionType::CAMERA_ROLL,
                        //                     'label' => 'Camera roll',
                        //                 ]),
                        //             ]),
                        //             new QuickReplyItem([
                        //                 'type' => 'action',
                        //                 'action' => new PostbackAction([
                        //                     'type' => ActionType::POSTBACK,
                        //                     'label' => 'POSTBACK', // user への表示
                        //                     'text' => 'POSTBACK', // テキスト
                        //                     'data' => 'action=buy&itemid=123',
                        //                 ]),
                        //             ]),
                        //             new QuickReplyItem([
                        //                 'type' => 'action',
                        //                 'action' => new DatetimePickerAction([
                        //                     'type' => ActionType::DATETIME_PICKER,
                        //                     'label' => 'Select date',
                        //                     'data' => 'storeId=12345',
                        //                     'mode' => 'datetime',
                        //                     'initial' => '2017-12-25t00:00',
                        //                     'max' => '2018-01-24t23:59',
                        //                     'min' => '2017-12-25t00:00',
                        //                 ]),
                        //             ]),
                        //         ]
                        //     ]);

                        //     $message = new TextMessage([
                        //         'text' => '$ click button! $',
                        //         'type' => MessageType::TEXT,
                        //         'emojis' => [
                        //             new Emoji([
                        //                 'index' => 0,
                        //                 'productId' => '5ac1bfd5040ab15980c9b435',
                        //                 'emojiId' => '001',
                        //             ]),
                        //             new Emoji([
                        //                 'index' => 16,
                        //                 'productId' => '5ac1bfd5040ab15980c9b435',
                        //                 'emojiId' => '001',
                        //             ]),
                        //         ],
                        //         'quickReply' => $quickReply,
                        //     ]);
                        //     $messagingApi->replyMessage([
                        //         'replyToken' => $replyToken,
                        //         'messages' => [
                        //             $message,
                        //         ],
                        //     ]);
                        //     break;
                        // } // test finished ...
                    }
                    break;
                case "postback": //postbackイベント
                    Log::debug("action: postback");
                    $userProfileResponse = $messagingApi->getProfile(
                        userId: $event["source"]["userId"]
                    );
                    $lineId = $userProfileResponse->getUserId();
                    Log::debug("line_id: " . $lineId);
                    $lineInfo = $this->findOrCreateLineInfo(
                        $lineId,
                        $userProfileResponse,
                        $manager
                    );
                    $replyToken = $event["replyToken"];
                    // 注意: $replyToken での返答は一度しか使えない
                    if ($lineInfo->sync_step_cd == 0) {
                        $this->sendNextQuestion(
                            $lineInfo,
                            $messagingApi,
                            $replyToken
                        );
                    } else {
                        // 一つ前の質問を表示
                        $lineInfo->sync_step_cd -= 1; // sync_step_cdをマイナス1して1つ前の質問を表示
                        $lineInfo->save();

                        // sync_step_cdの値に基づいて前の質問を取得
                        $previousQuestion = Question::where(
                            "manager_id",
                            $lineInfo->manager_id
                        )
                            ->orderBy("sort_num", "asc")
                            ->skip($lineInfo->sync_step_cd) // sync_step_cdに基づいて前の質問を取得
                            ->first();

                        if (!$previousQuestion) {
                            // 前の質問がない場合、エラーメッセージを送信
                            // $message = new TextMessage(['type' => 'text','text' => 'これ以上戻れません。']);
                            // $messagingApi->replyMessage([
                            //     'replyToken' => $replyToken,
                            //     'messages' => [$message],
                            // ]);
                            // return;
                        }

                        // 前の質問をQuick Replyとともに再送信
                        $this->sendQuestionWithQuickReply(
                            $previousQuestion,
                            $messagingApi,
                            $replyToken,
                            "前の質問に戻ります。",
                            $lineInfo->sync_step_cd
                        );
                    }
                    return;
                case "follow": //お友達追加時
                case "join": //グループに入ったときのイベント
                case "beacon": //Beaconイベント
                default:
            }
            return;
        }

        // if (isset($events) && is_array($events)) {

        //     foreach ($events as $event) {
        //         // おうむ返し

        //         //
        //         if (isset($event["replyToken"])) {
        //             $replyToken = $event["replyToken"];
        //         //     Log::debug("ReplyToken: " . $replyToken);

        //             $client = new \GuzzleHttp\Client();
        //             $config = new Configuration();
        //             $config->setAccessToken($channel_token);
        //             $messagingApi = new MessagingApiApi(
        //                 client: $client,
        //                 config: $config
        //             );
        //             $userProfileResponse = $messagingApi->getProfile(
        //                 userId: $event["source"]["userId"]
        //             );
        //         //     Log::debug($userProfileResponse);

        //         //     // ユーザーの LINE ID に基づいたデータを取得または作成(「アップサート」操作)
        //             $lineId = $userProfileResponse->getUserId();
        //             $lineInfo = LineInfo::where("line_id", $lineId)->first();

        //             if ($lineInfo) {
        //         //         // データが既に存在すれば更新
        //                 // $lineInfo->update([
        //                 //     "displayName" => $userProfileResponse->getDisplayName(),
        //                 //     "language" => $userProfileResponse->getLanguage(),
        //                 //     "pictureUrl" => $userProfileResponse->getPictureUrl(),
        //                 //     "statusMessage" => $userProfileResponse->getStatusMessage(),
        //                 // ]);
        //                 $userMessage = $event["message"]["text"];
        //                 $replyText = $this->handleUserMessage(
        //                     $userMessage,
        //                     $lineInfo,
        //                     $manager_id
        //                 );
        //             }
        //         //  else {
        //         //         // データが存在しなければ新規作成
        //         //         $user = User::create([
        //         //             "last_name" => "LastName",
        //         //             "first_name" => "FirstName",
        //         //             "last_name_kana" => "LastNameKana",
        //         //             "first_name_kana" => "FirstNameKana",
        //         //             "email" => "email@example.com",
        //         //             "phone_number" => "1234567890",
        //         //         ]);

        //         //         $user_id = $user->id;
        //         //         Log::debug("30f94");
        //         //         Log::debug($user_id);
        //                 // $lineInfo = LineInfo::create([
        //                 //     "line_id" => $lineId,
        //                 //     "displayName" => $userProfileResponse->getDisplayName(),
        //                 //     "language" => $userProfileResponse->getLanguage(),
        //                 //     "pictureUrl" => $userProfileResponse->getPictureUrl(),
        //                 //     "statusMessage" => $userProfileResponse->getStatusMessage(),
        //                 //     "user_id" => $user_id,
        //                 // ]);
        //         //     }

        //         //     Log::debug($request);

        //         //     try {
        //         //         $response = $messagingApi->replyMessage($request);
        //         //         // Success
        //         //     } catch (MessagingApiApi $e) {
        //         //         // Failed
        //         //         Log::error(
        //         //             "Error Log: " .
        //         //                 $e->getCode() .
        //         //                 " " .
        //         //                 $e->getResponseBody()
        //         //         );
        //         //     }
        //         }
        //     }
        // }
    }

    // private function handleUserMessage($userMessage, $lineInfo, $manager_id)
    // {
    // 最初の質問を取得
    // $question = Question::where("manager_id", $manager_id)
    //     ->orderBy("sort_num", "asc")
    //     ->first();

    // // 質問が存在しない場合
    // if (!$question) {
    //     return "マネージャーに紐付いた質問が見つかりませんでした。もしくはセットされていません。";
    // }

    // $quizState->current_question_id = $question->id;
    // $quizState->question_phase = 1;
    // $quizState->save();

    // // 質問と選択肢を表示
    // $options = $question
    //     ->options()
    //     ->pluck("value")
    //     ->toArray();
    // return $question->question .
    //     "\n選択肢:\n" .
    //     implode("\n", $options);

    // // 現在の質問に対する回答の処理
    // $currentQuestion = Question::find($quizState->current_question_id);
    // if ($currentQuestion) {
    //     // 選択肢をチェック
    //     $option = $currentQuestion
    //         ->options()
    //         ->where("value", $userMessage)
    //         ->first();
    //     if (!$option) {
    //         $options = $currentQuestion
    //             ->options()
    //             ->pluck("value")
    //             ->toArray();
    //         return "選択肢から選んでください:\n" .
    //             implode("\n", $options);
    //     }

    //     // 問題なく回答

    //     // 回答を保存
    //     Answer::create([
    //         "user_id" => $lineInfo->user_id,
    //         "question_id" => $currentQuestion->id,
    //         "option_id" => $option->id,
    //     ]);

    //     // 次の質問を取得
    //     $nextQuestion = Question::where(
    //         "sort_num",
    //         ">",
    //         $currentQuestion->sort_num
    //     )
    //         ->where("manager_id", $manager_id)
    //         ->orderBy("sort_num", "asc")
    //         ->first();
    //     if ($nextQuestion) {
    //         $quizState->current_question_id = $nextQuestion->id;
    //         $quizState->question_phase = $quizState->question_phase + 1;
    //         $quizState->save();

    //         // 質問と選択肢を表示
    //         $options = $nextQuestion
    //             ->options()
    //             ->pluck("value")
    //             ->toArray();
    //         return "次の質問です。\n\n" .
    //             $nextQuestion->question .
    //             "\n\n選択肢:\n" .
    //             implode("\n", $options);
    //     } else {
    //         $quizState->current_question_id = 999;
    //         $quizState->question_phase = 999;
    //         $quizState->save();
    //         // 全ての質問が終わったら結果を表示
    //         return $this->showResult($lineInfo, $manager_id);
    //     }
    // } else {
    //     return "error(エラーコード: 3000)";
    // }
    // }

    // private function showResult($lineInfo = null, $manager_id = null)
    // {
    //     // ここで結果を計算・表示するロジックを書く
    //     return "クイズが終了しました。結果を表示";
    // }

    private function findOrCreateLineInfo(
        $lineId,
        $userProfileResponse,
        $manager
    ) {
        $lineInfo = LineInfo::where("line_id", $lineId)
            ->where("manager_id", $manager->id)
            ->first();
        Log::debug("lineInfo: ");
        Log::debug($lineInfo);

        if ($lineInfo) {
            $lineInfo->update([
                "displayName" => $userProfileResponse->getDisplayName(),
                "language" => $userProfileResponse->getLanguage(),
                "pictureUrl" => $userProfileResponse->getPictureUrl(),
                "statusMessage" => $userProfileResponse->getStatusMessage(),
                "manager_id" => $manager->id,
            ]);
        } else {
            $lineInfo = LineInfo::create([
                "line_id" => $lineId,
                "displayName" => $userProfileResponse->getDisplayName(),
                "language" => $userProfileResponse->getLanguage(),
                "pictureUrl" => $userProfileResponse->getPictureUrl(),
                "statusMessage" => $userProfileResponse->getStatusMessage(),
                "manager_id" => $manager->id,
            ]);
        }

        return $lineInfo;
    }

    private function sendQuestionWithQuickReply(
        $question,
        $messagingApi,
        $replyToken,
        $errorMessage,
        $order
    ) {
        Log::debug("sendQuestionWithQuickReply: ");
        Log::debug($question);
        // エラーメッセージを送信
        $error_message = new TextMessage([
            "type" => "text",
            "text" => $errorMessage,
        ]);
        $order_message = new TextMessage([
            "type" => "text",
            "text" => $order + 1 . "問目の質問です。",
        ]);

        $items = [];
        foreach ($question->options as $option) {
            Log::debug($option->value);
            $items[] = new QuickReplyItem([
                "type" => "action",
                "imageUrl" => "https://example.com/sushi.png",
                "action" => [
                    "type" => "message",
                    "label" => $option->value,
                    "text" => $option->value,
                ],
            ]);
        }

        $quickReply = new QuickReply(["items" => $items]);

        $message = new TextMessage([
            "text" => $question->question,
            "type" => MessageType::TEXT,
            "quickReply" => $quickReply,
        ]);

        $messagingApi->replyMessage([
            "replyToken" => $replyToken,
            "messages" => [$error_message, $order_message, $message],
        ]);
    }

    private function sendNextQuestion($lineInfo, $messagingApi, $replyToken)
    {
        Log::debug("sendNextQuestion: ");
        // sync_step_cd の値に基づいて次の質問を取得する
        $nextQuestion = Question::where("manager_id", $lineInfo->manager_id)
            ->orderBy("sort_num", "asc")
            ->skip($lineInfo->sync_step_cd) // sync_step_cd に基づいて次の質問を取得
            ->first();

        if (!$nextQuestion) {
            // もし次の質問がない場合、ユーザーに終了メッセージを送信
            $message = new TextMessage([
                "type" => "text",
                "text" => "すべての質問が終了しました。",
            ]);
            $messagingApi->replyMessage([
                "replyToken" => $replyToken,
                "messages" => [$message],
            ]);
            return;
        }

        // Quick Replyの項目を作成
        $items = [];
        foreach ($nextQuestion->options as $option) {
            $items[] = new QuickReplyItem([
                "type" => "action",
                "imageUrl" => "https://example.com/sushi.png",
                "action" => [
                    "type" => "message",
                    "label" => $option->value,
                    "text" => $option->value,
                ],
            ]);
        }

        $quickReply = new QuickReply(["items" => $items]);

        $message = new TextMessage([
            "text" => $nextQuestion->question,
            "type" => MessageType::TEXT,
            "quickReply" => $quickReply,
        ]);
        $messagingApi->replyMessage([
            "replyToken" => $replyToken,
            "messages" => [$message],
        ]);
    }
}
