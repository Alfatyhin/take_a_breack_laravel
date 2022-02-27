<?php

namespace App\Http\Controllers;

use App\Models\WebhookLog;
use App\Services\EcwidService;
use Symfony\Component\HttpFoundation\Request;

class ApiRest extends Controller
{
    public function getEcwidProductBuCategoryId(Request $request)
    {
        header('Access-Control-Allow-Origin: *');

        $id = $request->get('id');
        $ecwidService = new EcwidService();
        $products = $ecwidService->getProductsByCategoryId($id);

        foreach ($products['items'] as $item) {
            $productId = $item['id'];
            $productList[$productId] = $productId;
        }

        return json_encode($productList);
    }

    public function getEcwidCategories(Request $request)
    {
        header('Access-Control-Allow-Origin: *');

        $ecwidService = new EcwidService();
        $categories = $ecwidService->getCategories();

        return json_encode($categories);
    }

    public function getIP(Request $request)
    {

        $res = $this->getIPAddress();

        return json_encode($res);
    }

    private function getIPAddress() {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip['HTTP_CLIENT_IP'] = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip['HTTP_X_FORWARDED_FOR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        $ip['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];

        return $ip;
    }

    public function testRequest(Request $request)
    {
        $headers = $request->header();
        $log['headers'] = $headers;
        $post = $request->post();
        if (is_array($post)) {
            $log['type post'] = 'array';
        } else {
            $log['type post'] = 'unknown';
        }
        $log['post body'] = $post;
        $get = $request->toArray();

        $log['get params'] = $get;
        http_response_code(200);


//        WebhookLog::addLog('tes request', $log);

        echo  json_encode($log);

    }
}
