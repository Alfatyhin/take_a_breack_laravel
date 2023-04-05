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
