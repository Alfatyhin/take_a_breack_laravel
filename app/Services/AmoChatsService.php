<?php


namespace App\Services;


use DateTimeInterface;

class AmoChatsService
{
    private $id = "ad611214-c242-4225-8304-2f3bdb946253";
    private $code = "amo.ext.29039599";
    private $account_id;
    private $secret;
    private $scopeId;

    public function __construct()
    {
        $this->account_id = env('AMO_JO_ID');
        $this->secret =  env('AMO_JO_SECRET');
        $this->scopeId = $this->id.'_'.$this->account_id;
    }

    public function connectChanel()
    {
        $apiMethod = sprintf('/v2/origin/custom/%s/connect', $this->id);
        $body = [
            'account_id' => $this->account_id,
            'title' => 'TakeaBreakChat',
            'hook_api_version' => 'v2',
        ];

        $requestBody = json_encode($body);
        $checkSum  = $this->createBodyChecksum($requestBody);
        $signature = $this->createSignature($this->secret, $checkSum, $apiMethod);
        $curlHeaders = $this->prepareHeaderForCurl($checkSum, $signature);

        $res = $this->execCurl($apiMethod, $requestBody, $curlHeaders);

        dd($res, $curlHeaders, $signature, $requestBody);

    }

    public function newIncomingMessage(array $data)
    {
        $apiMethod = sprintf('/v2/origin/custom/%s', $this->scopeId);
        $requestBody = [
            'event_type' => 'new_message',
            'payload' => [
                'timestamp' => time(),
                'msec_timestamp' => round(microtime(true) * 1000),
                'msgid' => $data['msgid'],
                'conversation_id' => $data['chat_id'],
                'sender' => [
                    'id' => $data['client_id'],
//                    'avatar' => 'https://images.pexels.com/photos/10050979/pexels-photo-10050979.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=500',
                    'profile' => [
                        'phone' => $data['client_phone'],
                    ],
//                    'profile_link' => 'https://example.com/profile/example.client',
                    'name' => $data['client_name'],
                ],
                'message' => $data['message'],
//                "source" => [
//                    "external_id" => '79039876543'
//                ],
                'silent' => $data['silent'],
            ],
        ];

        $jsonBody = json_encode($requestBody);
        $checkSum  = $this->createBodyChecksum($jsonBody);
        $signature = $this->createSignature($this->secret, $checkSum, $apiMethod);

        // Подготовим заголовки
        $curlHeaders = $this->prepareHeaderForCurl($checkSum, $signature);
        $res = $this->execCurl($apiMethod, $jsonBody, $curlHeaders);

        return $res;
    }

    public function messageStatus(array $data)
    {
        $apiMethod = sprintf('/v2/origin/custom/%s/%s/delivery_status', $this->scopeId, $data['msgid']);
        $requestBody = [
            'msgid' => $data['msgid'],
            'delivery_status' => $data['delivery_status'],
            'error_code' => $data['error_code'],
            'error' =>  $data['error']
        ];

        $jsonBody = json_encode($requestBody);
        $checkSum  = $this->createBodyChecksum($jsonBody);
        $signature = $this->createSignature($this->secret, $checkSum, $apiMethod);

        // Подготовим заголовки
        $curlHeaders = $this->prepareHeaderForCurl($checkSum, $signature);
        $res = $this->execCurl($apiMethod, $jsonBody, $curlHeaders);

        return $res;
    }

    public function newChat()
    {
        $apiMethod = sprintf('/v2/origin/custom/%s/chats', $this->scopeId);
        $requestBody = [
            'conversation_id' => '8e3e7640-49af-4448-a2c6-d5a421f7f512',
//            'source' => [
//                'external_id' => '78001234567', // external_id источника в API Источников, поле не передается, если интеграция не поддерживает множественные источники
//            ],
            'user' => [
                'id' => '127',
//                'avatar' => 'https://example.com/users/avatar.png',
                'name' => 'Alexander',
                'profile' => [
                    'phone' => '+380992363774',
//                    'email' => 'example.client@example.com',
                ],
//                'profile_link' => 'https://example.com/profile/example.client',
            ]
        ];

        $jsonBody = json_encode($requestBody);
        $checkSum  = $this->createBodyChecksum($jsonBody);
        $signature = $this->createSignature($this->secret, $checkSum, $apiMethod);

        // Подготовим заголовки
        $curlHeaders = $this->prepareHeaderForCurl($checkSum, $signature);
        $res = $this->execCurl($apiMethod, $jsonBody, $curlHeaders);

        dd($res, $curlHeaders, $signature, $requestBody, $this->secret);

    }

    private function createBodyChecksum(string $body): string
    {
        return md5($body);
    }

    /**
     * Расчитываем подпись запроса
     *
     * @param string $secret Секретный ключ вашего канала
     * @param string $checkSum Рассчитанный хэш тела запроса
     * @param string $apiMethod Адрес вызываемого метода API
     * @param string $httpMethod HTTP метод запроса
     * @param string $contentType Передаваемый тип данных
     *
     * @return string
     */
    private function createSignature(string $secret, string $checkSum, string $apiMethod, string $httpMethod = 'POST', string $contentType = 'application/json'): string {
        $str = implode("\n", [
            strtoupper($httpMethod),
            $checkSum,
            $contentType,
            date(DateTimeInterface::RFC2822),
            $apiMethod,
        ]);

        return hash_hmac('sha1', $str, $secret);
    }


    private function prepareHeaderForCurl(
        string $checkSum,
        string $signature,
        string $contentType = 'application/json'
    ): array {
        $headers = [
            'Date' => date(DateTimeInterface::RFC2822),
            'Content-Type' => $contentType,
            'Content-MD5' => strtolower($checkSum),
            'X-Signature' => strtolower($signature),
            'User-Agent' => 'amoCRM-Chats-Doc-Example/1.0'
        ];

        foreach ($headers as $name => $value) {
            $curlHeaders[] = $name . ": " . $value;
        }

        return $curlHeaders;
    }

    /**
     * Выполняем запрос к API Чатов
     *
     * @param string $apiMethod Запрашиваемый метод API
     * @param string $requestBody Тело запроса
     * @param array $requestHeaders Заголовки запроса
     * @param string $httpMethod HTTP метод запроса
     */
    private function execCurl(string $apiMethod, string $requestBody, array $requestHeaders, string $httpMethod = 'POST')
    {
        $curl = curl_init();
        $curlOptions = [
            CURLOPT_URL => 'https://amojo.amocrm.ru' . $apiMethod,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_HTTPHEADER => $requestHeaders,
        ];

        if (!empty($requestBody)) {
            $curlOptions[CURLOPT_POSTFIELDS] = $requestBody;
        }

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if ($error) {
            return ['error' => $error, 'response' => $response, 'info' => $info];
        } else {
            return $response;
        }
    }


}