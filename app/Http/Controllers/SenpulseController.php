<?php

namespace App\Http\Controllers;

use App\Models\WebhookLog;
use App\Services\AmoChatsService;
use App\Services\AmoCrmServise;
use App\Services\SendpulseService;
use Illuminate\Http\Request;

class SenpulseController extends Controller
{

    public function whatsapp(Request $request)
    {


    }

    public function incommingChatMessage(Request $request)
    {
        $data = $request->json()->all();

        http_response_code(200);
        $webhook = new WebhookLog();
        $webhook->name = 'SenpulseController - incommingChatMessage';
        $webhook->data = json_encode($data);
        $webhook->save();

        $AmoChatsService = new AmoChatsService();

        foreach ($data as $item) {
            $title = $item['title'];
            if ($title == 'incoming_message') {

                $data_message = [
                    'silent' => false,
                    'msgid' => $item['info']['message']['id'],
                    'chat_id' => $item['contact']['id'],
                    'client_name' => $item['contact']['name'],
                    'client_phone' => '+'.$item['contact']['phone'],
                    'client_id' => $item['contact']['id'],
                ];

                if($item['info']['message']['channel_data']['message']['type'] == "text") {
                    $message = [
                        'type' =>  'text',
                        'text' =>  $item['info']['message']['channel_data']['message']['text']['body']
                    ];
                } elseif ($item['info']['message']['channel_data']['message']['type'] == "image") {
                    $message = [
                        'type' =>  'text',
                        'text' =>  $item['info']['message']['channel_data']['message']['image']['caption']
                            . "\n" .  $item['info']['message']['channel_data']['message']['image']['url'],
                    ];
                } elseif ($item['info']['message']['channel_data']['message']['type'] == "audio") {
                    $message = [
                        'type' =>  'text',
                        'text' =>  $item['info']['message']['channel_data']['message']['audio']['url'],
                    ];
                } elseif ($item['info']['message']['channel_data']['message']['type'] == "unsupported") {
                    $message = [
                        'type' =>  'text',
                        'text' =>  '/unsupported - ' .  $item['info']['message']['channel_data']['message']['errors'][0]['title'],
                    ];
                } elseif ($item['info']['message']['channel_data']['message']['type'] == "reaction") {
                    $message = [
                        'type' =>  'text',
                        'text' =>  '/reaction - ' .  $item['info']['message']['channel_data']['message']['reaction']['emoji'],
                    ];
                } elseif ($item['info']['message']['channel_data']['message']['type'] == "document") {
                    $message = [
                        'type' =>  'text',
                        'text' =>  $item['info']['message']['channel_data']['message']['document']['caption']
                            . "\n" . $item['info']['message']['channel_data']['message']['document']['filename']
                            . ' - ' .  $item['info']['message']['channel_data']['message']['document']['url'],
                    ];
                } elseif ($item['info']['message']['channel_data']['message']['type'] == "contacts") {
                    $message = [
                        'type' =>  'text',
                        'text' => '/contacts',
                    ];
                } else {
                    $message = [
                        'type' =>  'text',
                        'text' =>  "/не определено \n" .  $item['contact']['last_message'],
                    ];
                }

                $data_message['message'] = $message;


                $res = $AmoChatsService->newIncomingMessage($data_message);
                $webhook = new WebhookLog();
                $webhook->name = 'AmoChatsService - result new message';
                $webhook->data = $res;
                $webhook->save();

            }

            if ($title == 'outgoing_message' && $item['contact']['phone'] == '380992363774') {

            }

        }

    }

    public function testIncommingChatMessage(Request $request)
    {
        $data = '{"time": 1681738775, "message": {"sender": {"id": "23105542-4b61-4ec8-9bb0-f9a4f532941a"}, "message": {"id": "e342407d-35f0-405b-8195-bdab482bb193", "tag": null, "text": "Елена добрый день! Ваш заказ будет доставлен завтра.", "type": "text", "media": null, "markup": null, "file_name": null, "file_size": 0, "thumbnail": null}, "receiver": {"id": "2be900e2-bd19-46f4-b4e4-20e937a4977f", "name": null, "email": null, "phone": "972547878530"}, "timestamp": 1681738775, "conversation": {"id": "a90bc89d-3b54-44a0-9c09-970c61a90928"}, "msec_timestamp": 1681738775273}, "account_id": "9d01068c-85a5-413a-87bd-79a69e6e9060"}';


        $AmoChatsService = new AmoChatsService();

        foreach ($data as $item) {
            $title = $item['title'];
            if ($title == 'incoming_message') {

                $data_message = [
                    'silent' => false,
                    'msgid' => $item['info']['message']['id'],
                    'chat_id' => $item['contact']['id'],
                    'client_name' => $item['contact']['name'],
                    'client_phone' => '+'.$item['contact']['phone'],
                    'client_id' => $item['contact']['id'],
                ];

                if($item['info']['message']['channel_data']['message']['type'] == "text") {
                    $message = [
                        'type' =>  'text',
                        'text' =>  $item['info']['message']['channel_data']['message']['text']['body']
                    ];
                } elseif ($item['info']['message']['channel_data']['message']['type'] == "image") {
                    $message = [
                        'type' =>  'text',
                        'text' =>  $item['info']['message']['channel_data']['message']['image']['caption']
                            . "\n" .  $item['info']['message']['channel_data']['message']['image']['url'],
                    ];
                } elseif ($item['info']['message']['channel_data']['message']['type'] == "audio") {
                    $message = [
                        'type' =>  'text',
                        'text' =>  $item['info']['message']['channel_data']['message']['audio']['url'],
                    ];
                } elseif ($item['info']['message']['channel_data']['message']['type'] == "unsupported") {
                    $message = [
                        'type' =>  'text',
                        'text' =>  '/unsupported - ' .  $item['info']['message']['channel_data']['message']['errors'][0]['title'],
                    ];
                } elseif ($item['info']['message']['channel_data']['message']['type'] == "reaction") {
                    $message = [
                        'type' =>  'text',
                        'text' =>  '/reaction - ' .  $item['info']['message']['channel_data']['message']['reaction']['emoji'],
                    ];
                } elseif ($item['info']['message']['channel_data']['message']['type'] == "document") {
                    $message = [
                        'type' =>  'text',
                        'text' =>  $item['info']['message']['channel_data']['message']['document']['caption']
                            . "\n" . $item['info']['message']['channel_data']['message']['document']['filename']
                            . ' - ' .  $item['info']['message']['channel_data']['message']['document']['url'],
                    ];
                } elseif ($item['info']['message']['channel_data']['message']['type'] == "contacts") {
                    $message = [
                        'type' =>  'text',
                        'text' => '/contacts',
                    ];
                } else {
                    $message = [
                        'type' =>  'text',
                        'text' =>  "/не определено \n" .  $item['contact']['last_message'],
                    ];
                }

                $data_message['message'] = $message;


                $res = $AmoChatsService->newIncomingMessage($data_message);
                $webhook = new WebhookLog();
                $webhook->name = 'AmoChatsService - result new message';
                $webhook->data = $res;
                $webhook->save();

            }

            if ($title == 'outgoing_message' && $item['contact']['phone'] == '380992363774') {

            }

        }

    }
}
