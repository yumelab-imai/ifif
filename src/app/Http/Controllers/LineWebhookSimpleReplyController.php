<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use Illuminate\Support\Facades\Log;

use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Configuration;

class LineWebhookSimpleReplyController extends Controller
{
    // メイン機能
    public function __invoke(Request $request)
    {
        $events = $request->events;
        // Create the bot client instance
        $client = new \GuzzleHttp\Client();
        $config = new Configuration();
        $config->setAccessToken("properly channel_token");
        $messagingApi = new MessagingApiApi(client: $client, config: $config);

        foreach ($events as $event) {
            Log::debug($event["replyToken"]);
            $message = new TextMessage(["type" => "text", "text" => "hello!"]);
            $request = new ReplyMessageRequest([
                "replyToken" => $event["replyToken"],
                "messages" => [$message],
            ]);

            $response = $messagingApi->replyMessage($request);
            return; // ここで処理を終了する
        }
    }
}
