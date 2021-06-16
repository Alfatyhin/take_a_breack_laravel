<?php

namespace App\Http\Controllers;

use App\Services\EcwidService;
use Illuminate\Http\Request;

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
}
